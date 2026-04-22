<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class JwtService
{
    private const ALGO = 'HS256';
    private const ISSUER = 'studio-supply-api';

    public function issueToken(User $user): string
    {
        $now = now();
        $expiresAt = $now->clone()->addMinutes($this->ttlMinutes());
        $payload = [
            'iss' => self::ISSUER,
            'sub' => (string) $user->id,
            'iat' => $now->timestamp,
            'exp' => $expiresAt->timestamp,
            'jti' => (string) Str::uuid(),
        ];

        return $this->encode($payload);
    }

    public function userFromToken(?string $token): ?User
    {
        if (! is_string($token) || trim($token) === '') {
            return null;
        }

        $payload = $this->decode($token);
        if ($payload === null) {
            return null;
        }

        if (($payload['iss'] ?? null) !== self::ISSUER) {
            return null;
        }

        $exp = $payload['exp'] ?? null;
        if (! is_int($exp) || $exp <= now()->timestamp) {
            return null;
        }

        $jti = $payload['jti'] ?? null;
        if (is_string($jti) && Cache::has($this->blacklistKey($jti))) {
            return null;
        }

        $subject = $payload['sub'] ?? null;
        if (! is_string($subject) || ! ctype_digit($subject)) {
            return null;
        }

        return User::query()->find((int) $subject);
    }

    public function invalidateToken(string $token): void
    {
        $payload = $this->decode($token);
        if ($payload === null) {
            return;
        }

        $jti = $payload['jti'] ?? null;
        $exp = $payload['exp'] ?? null;
        if (! is_string($jti) || ! is_int($exp)) {
            return;
        }

        $seconds = max(1, $exp - now()->timestamp);
        Cache::put($this->blacklistKey($jti), true, now()->addSeconds($seconds));
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function encode(array $payload): string
    {
        $header = ['typ' => 'JWT', 'alg' => self::ALGO];
        $segments = [
            $this->base64UrlEncode(json_encode($header, JSON_THROW_ON_ERROR)),
            $this->base64UrlEncode(json_encode($payload, JSON_THROW_ON_ERROR)),
        ];
        $signature = hash_hmac('sha256', implode('.', $segments), $this->secret(), true);
        $segments[] = $this->base64UrlEncode($signature);

        return implode('.', $segments);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decode(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$encodedHeader, $encodedPayload, $encodedSignature] = $parts;
        $headerRaw = $this->base64UrlDecode($encodedHeader);
        $payloadRaw = $this->base64UrlDecode($encodedPayload);
        $signature = $this->base64UrlDecode($encodedSignature);

        if ($headerRaw === false || $payloadRaw === false || $signature === false) {
            return null;
        }

        $header = json_decode($headerRaw, true);
        $payload = json_decode($payloadRaw, true);

        if (
            ! is_array($header)
            || ! is_array($payload)
            || ($header['alg'] ?? null) !== self::ALGO
        ) {
            return null;
        }

        $expected = hash_hmac('sha256', "{$encodedHeader}.{$encodedPayload}", $this->secret(), true);
        if (! hash_equals($expected, $signature)) {
            return null;
        }

        return $payload;
    }

    private function secret(): string
    {
        $key = config('app.key');
        if (! is_string($key) || $key === '') {
            throw new \RuntimeException('APP_KEY is required for JWT.');
        }

        if (str_starts_with($key, 'base64:')) {
            $decoded = base64_decode(substr($key, 7), true);
            if ($decoded === false) {
                throw new \RuntimeException('APP_KEY base64 decoding failed.');
            }

            return $decoded;
        }

        return $key;
    }

    private function ttlMinutes(): int
    {
        $ttl = config('auth.jwt_ttl_minutes', 120);

        return is_numeric($ttl) ? max(1, (int) $ttl) : 120;
    }

    private function blacklistKey(string $jti): string
    {
        return "jwt:blacklist:{$jti}";
    }

    private function base64UrlEncode(string $input): string
    {
        return rtrim(strtr(base64_encode($input), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $input): string|false
    {
        $padding = strlen($input) % 4;
        if ($padding > 0) {
            $input .= str_repeat('=', 4 - $padding);
        }

        return base64_decode(strtr($input, '-_', '+/'), true);
    }
}
