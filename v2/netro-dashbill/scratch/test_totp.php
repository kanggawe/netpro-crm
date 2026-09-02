<?php
class TOTP {
    public static function base32Decode($base32) {
        $base32 = strtoupper(str_replace([' ', '-'], '', $base32));
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $buffer = 0;
        $bitsLeft = 0;
        $binary = '';
        for ($i = 0; $i < strlen($base32); $i++) {
            $val = strpos($chars, $base32[$i]);
            if ($val === false) continue;
            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;
            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $binary .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }
        return $binary;
    }

    public static function generateSecret($length = 16) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        $bytes = random_bytes($length);
        for ($i = 0; $i < $length; $i++) {
            $secret .= $chars[ord($bytes[$i]) % 32];
        }
        return $secret;
    }

    public static function getCode($secret, $timeSlice = null) {
        if ($timeSlice === null) {
            $timeSlice = floor(time() / 30);
        }
        $secretKey = self::base32Decode($secret);
        $time = pack('N*', 0) . pack('N*', $timeSlice);
        $hmac = hash_hmac('sha1', $time, $secretKey, true);
        $offset = ord(substr($hmac, -1)) & 0x0F;
        $hashpart = substr($hmac, $offset, 4);
        $value = unpack('N', $hashpart);
        $value = $value[1] & 0x7FFFFFFF;
        return str_pad($value % 1000000, 6, '0', STR_PAD_LEFT);
    }

    public static function verifyCode($secret, $code, $discrepancy = 1) {
        $code = trim(str_replace([' ', '-'], '', $code));
        if (strlen($code) !== 6 || !ctype_digit($code)) {
            return false;
        }
        $currentTimeSlice = floor(time() / 30);
        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calculated = self::getCode($secret, $currentTimeSlice + $i);
            if (hash_equals($calculated, $code)) {
                return true;
            }
        }
        return false;
    }

    public static function getQrUrl($label, $secret, $issuer = 'NETPRO CRM') {
        $encodedLabel = rawurlencode($issuer . ':' . $label);
        $encodedIssuer = rawurlencode($issuer);
        $encodedSecret = rawurlencode($secret);
        $otpauth = "otpauth://totp/{$encodedLabel}?secret={$encodedSecret}&issuer={$encodedIssuer}&period=30&digits=6";
        return "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($otpauth);
    }
}

$secret = TOTP::generateSecret();
$code = TOTP::getCode($secret);
echo "Generated Secret: " . $secret . PHP_EOL;
echo "Current TOTP Code: " . $code . PHP_EOL;
echo "Verification Test: " . (TOTP::verifyCode($secret, $code) ? "PASS" : "FAIL") . PHP_EOL;
echo "Wrong Code Test: " . (!TOTP::verifyCode($secret, "000000") ? "PASS" : "FAIL") . PHP_EOL;
echo "QR Code URL: " . TOTP::getQrUrl('admin@netpro.id', $secret) . PHP_EOL;
