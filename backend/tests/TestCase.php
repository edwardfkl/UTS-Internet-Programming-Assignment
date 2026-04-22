<?php

namespace Tests;

use App\Models\User;
use App\Support\JwtService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function jwtTokenFor(User $user): string
    {
        /** @var JwtService $jwt */
        $jwt = app(JwtService::class);

        return $jwt->issueToken($user);
    }
}
