<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdminLocales;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', 'in:'.implode(',', AdminLocales::ALLOWED)],
        ]);

        $minutes = 60 * 24 * 365;

        return redirect()
            ->back()
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
