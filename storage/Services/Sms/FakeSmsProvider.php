<?php

namespace App\Services\Sms;

class FakeSmsProvider
{
    public function send(string $phone, string $code): bool
    {
        // In production, this is where Kavenegar/Twilio API calls go.
        // For now, we just log it to the terminal so you can test!
        
        logger()->info("🔥 [FAKE SMS] Sending OTP to {$phone}: {$code}");
        
        return true;
    }
}