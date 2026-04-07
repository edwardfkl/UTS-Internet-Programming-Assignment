<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminWebSessionController extends Controller
{
    /**
     * Create a web guard session so the same admin can use /admin (Blade) after SPA login.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user->is_admin) {
            abort(403, 'Not authorised for admin access.');
        }

        Auth::guard('web')->login($user, false);
        $request->session()->regenerate();

        return response()->json(['ok' => true]);
    }

    /**
     * Drop the web session (SPA logout should call this so /admin does not stay signed in).
     */
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user->is_admin) {
            return response()->json(['ok' => true]);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['ok' => true]);
    }
}
