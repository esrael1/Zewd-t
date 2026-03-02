<?php

namespace App\Services;

class JitsiTokenService
{
    /**
     * Generate a JWT token for Jitsi Meet.
     *
     * @param string $userName
     * @param string $roomName
     * @param bool $isModerator
     * @return string
     */
    public static function generateToken($userName, $roomName, $isModerator = false)
    {
        $appId = env('JITSI_APP_ID', 'zewd_app');
        $secret = env('JITSI_JWT_SECRET', 'zewd_jitsi_secret_key');

        $payload = [
            'iss' => $appId,
            'aud' => $appId,
            'sub' => $appId,
            'iat' => time(),
            'exp' => time() + 3600, // Token valid for 1 hour
            'room' => $roomName,
            'context' => [
                'user' => [
                    'name' => $userName,
                    'affiliation' => $isModerator ? 'owner' : 'member',
                ],
                'features' => [
                    'livestreaming' => true,
                    'recording' => true,
                    'transcription' => true,
                    'outbound-call' => false,
                ]
            ]
        ];

        return self::encodeHs256($payload, $secret);
    }

    private static function encodeHs256(array $payload, string $secret): string
    {
        $header = ['typ' => 'JWT', 'alg' => 'HS256'];

        $headerEncoded = self::base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES));
        $payloadEncoded = self::base64UrlEncode(json_encode($payload, JSON_UNESCAPED_SLASHES));
        $signature = hash_hmac('sha256', "{$headerEncoded}.{$payloadEncoded}", $secret, true);
        $signatureEncoded = self::base64UrlEncode($signature);

        return "{$headerEncoded}.{$payloadEncoded}.{$signatureEncoded}";
    }

    private static function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
