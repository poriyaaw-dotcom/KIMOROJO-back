<?php

namespace App\Services\Sms;

class FakeSmsProvider
{
    public function send(string $phone, string $code): bool
    {
        // SECURITY FIX: Never log actual OTP codes
        logger()->info("🔥 [FAKE SMS] OTP sent to {$phone}");
        error_log("🔥 [FAKE SMS] OTP sent to {$phone}");
        
        return true;
    }
}