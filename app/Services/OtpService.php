<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OtpService
{
    protected $expirySeconds = 300; // 5 min

    public function generateAndStore(string $mobile): string
    {
        // Pehle purana OTP (agar ho to) delete kar do
        Cache::forget("otp_mobile_{$mobile}");

        // Naya OTP banao aur store karo
        $otp = rand(100000, 999999);
        Cache::put("otp_mobile_{$mobile}", $otp, $this->expirySeconds);

        Log::info("New OTP generated for mobile {$mobile}: {$otp}");

        return (string) $otp; // ← Testing ke liye, production mein hata dena ya sirf admin/log ke liye rakhna
    }

    public function verify(string $mobile, string $otp): bool
    {
        $stored = Cache::get("otp_mobile_{$mobile}");

        if ($stored && $stored == $otp) {
            // Successful verify hone par turant delete (one-time use)
            Cache::forget("otp_mobile_{$mobile}");
            Log::info("OTP verified and removed for mobile: {$mobile}");
            return true;
        }

        Log::warning("Invalid OTP attempt for mobile: {$mobile}, entered: {$otp}");
        return false;
    }
}