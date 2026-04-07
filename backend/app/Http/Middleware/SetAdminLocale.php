<?php

namespace App\Http\Middleware;

use App\Support\AdminLocales;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetAdminLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->is('admin') && ! $request->is('admin/*')) {
            return $next($request);
        }

        $fromCookie = $request->cookie(AdminLocales::COOKIE);
        $locale = \is_string($fromCookie) && \in_array($fromCookie, AdminLocales::ALLOWED, true)
            ? $fromCookie
            : (string) config('app.locale', 'en');

        if (! \in_array($locale, AdminLocales::ALLOWED, true)) {
            $locale = 'en';
        }

        App::setLocale($locale);
        View::share('adminHtmlLang', AdminLocales::htmlLang($locale));
        View::share('adminIntlLocale', AdminLocales::intlLocale($locale));

        return $next($request);
    }
}
