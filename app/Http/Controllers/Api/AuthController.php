<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Otp;
use App\Models\FailedVerificationAttempt;
use App\Services\Sms\FakeSmsProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private function withCors($response)
    {
        return $response->header('Access-Control-Allow-Origin', 'http://localhost:3000')
                        ->header('Access-Control-Allow-Credentials', 'true')
                        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept');
    }

    public function sendOtp(Request $request)
    {
        // Normalize phone BEFORE validation (Fix #5)
        $phone = $this->normalizePhone($request->input('phone', ''));

        $request->validate([
            'phone' => ['required', 'string', 'regex:/^(\+98|0)9\d{9}$/']
        ]);

        $recentOtp = Otp::where('phone', $phone)
            ->where('created_at', '>', now()->subMinute())
            ->first();

        if ($recentOtp) {
            return $this->withCors(response()->json([
                'success' => false,
                'message' => 'Please wait 1 minute before requesting a new code.'
            ], 429));
        }

        $code = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(2);

        // Concurrency-safe OTP creation (Fix #6)
        DB::transaction(function () use ($phone, $code, $expiresAt) {
            // Delete old OTPs
            Otp::where('phone', $phone)->where('is_used', false)->delete();

            // Store hashed OTP (Fix #4)
            Otp::create([
                'phone' => $phone,
                'code' => Hash::make($code), // Hashed for security
                'expires_at' => $expiresAt,
                'is_used' => false,
            ]);
        });

        $smsProvider = new FakeSmsProvider();
        $smsProvider->send($phone, $code);

        return $this->withCors(response()->json([
            'success' => true,
            'message' => 'OTP sent successfully'
        ]));
    }

    public function verifyOtp(Request $request)
    {
        // Normalize phone BEFORE validation (Fix #5)
        $phone = $this->normalizePhone($request->input('phone', ''));

        $request->validate([
            'phone' => ['required', 'string', 'regex:/^(\+98|0)9\d{9}$/'],
            'code' => ['required', 'string', 'size:5', 'regex:/^[0-9]+$/']
        ]);

        $code = $request->code;

        // ==========================================
        // TEMPORARY BACKDOOR FOR TESTING
        // REMOVE THIS BLOCK BEFORE DEPLOYMENT!
        // ==========================================
        if ($code === '12345') {
            $user = User::firstOrCreate(
                ['phone' => $phone],
                [
                    'name' => 'Test User',
                    'email' => $phone . '@kimorojo.temp',
                    'password' => Hash::make(str()->random(16)),
                ]
            );
            
            $token = $user->createToken('auth_token')->plainTextToken;
            
            return $this->withCors(response()->json([
                'success' => true,
                'message' => 'Backdoor login successful',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'phone' => $user->phone,
                    'name' => $user->name,
                ]
            ]));
        }
        // ==========================================
        // END TEMPORARY BACKDOOR
        // ==========================================

        // Check server-side attempt limit (Issue 1.4)
        $recentAttempts = FailedVerificationAttempt::where('phone', $phone)
            ->where('created_at', '>', now()->subMinutes(15))
            ->count();

        if ($recentAttempts >= 5) {
            return $this->withCors(response()->json([
                'success' => false,
                'message' => 'Too many failed attempts. Please try again in 15 minutes.'
            ], 429));
        }

        return DB::transaction(function () use ($phone, $code, $request) {
            // Find the most recent unused, unexpired OTP for this phone
            $otp = Otp::where('phone', $phone)
                      ->where('is_used', false)
                      ->where('expires_at', '>', now())
                      ->latest()
                      ->first();

            // Verify using Hash::check instead of direct comparison (Fix #4)
            if (!$otp || !Hash::check($code, $otp->code)) {
                // Log failed attempt
                FailedVerificationAttempt::create([
                    'phone' => $phone,
                    'ip_address' => $request->ip(),
                ]);

                return $this->withCors(response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP'
                ], 401));
            }

            // Create user with dummy email (Issue 1.1)
            $user = User::firstOrCreate(
                ['phone' => $phone],
                [
                    'name' => 'User ' . substr($phone, -4),
                    'email' => $phone . '@kimorojo.temp',
                    'password' => Hash::make(str()->random(16)),
                ]
            );

            // Mark OTP as used ONLY after user creation succeeds
            $otp->update(['is_used' => true]);

            // Clear failed attempts on success
            FailedVerificationAttempt::where('phone', $phone)->delete();

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->withCors(response()->json([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'phone' => $user->phone,
                    'name' => $user->name,
                ]
            ]));
        });
    }

    private function normalizePhone(string $phone): string
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        
        $phone = str_replace($persian, $english, $phone);
        $phone = str_replace($arabic, $english, $phone);
        $phone = str_replace([' ', '-'], '', $phone);
        
        if (str_starts_with($phone, '+98')) {
            $phone = '0' . substr($phone, 3);
        }
        
        return $phone;
    }
}