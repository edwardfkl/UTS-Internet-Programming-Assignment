<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\AdminLocales;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocalePreferenceController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $raw = $request->cookie(AdminLocales::COOKIE);
        $locale = \is_string($raw) && \in_array($raw, AdminLocales::ALLOWED, true) ? $raw : null;

        return response()->json(['locale' => $locale]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', 'in:'.implode(',', AdminLocales::ALLOWED)],
        ]);

        $minutes = 60 * 24 * 365;

        return response()
            ->json(['locale' => $validated['locale']])
            ->cookie(
                AdminLocales::COOKIE,
                $validated['locale'],
                $minutes,
                '/',
                null,
                (bool) config('session.secure', false),
                true,
                false,
                'Lax'
            );
    }
}
