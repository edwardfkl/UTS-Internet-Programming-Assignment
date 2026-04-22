<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\Request;

final class SanctumBearer
{
    public static function user(Request $request): ?User
    {
        /** @var JwtService $jwt */
        $jwt = app(JwtService::class);

        return $jwt->userFromToken($request->bearerToken());
    }
}
