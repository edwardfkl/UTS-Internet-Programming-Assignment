<!DOCTYPE html>
<html lang="{{ $adminHtmlLang ?? 'en' }}" data-intl-locale="{{ $adminIntlLocale ?? 'en-AU' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('admin.login.title') }} — {{ __('admin.store_name') }}</title>
    @include('admin.partials.styles')
</head>
<body class="relative flex min-h-screen items-center justify-center bg-zinc-100 px-4 antialiased">
    <div class="absolute right-4 top-4">
        @include('admin.partials.language-menu')
    </div>
    <div class="w-full max-w-md rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm">
        <h1 class="text-xl font-semibold text-zinc-900">{{ __('admin.login.heading') }}</h1>
        <p class="mt-2 text-sm text-zinc-600">{{ __('admin.login.hint') }}</p>

        @if ($errors->any())
            <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-900">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="post" action="{{ route('admin.login') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-zinc-700">{{ __('admin.login.email') }}</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                       class="mt-1 w-full rounded-lg border border-zinc-300 px-3 py-2 text-zinc-900 shadow-sm focus:border-amber-800 focus:ring-amber-800/20">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-zinc-700">{{ __('admin.login.password') }}</label>
                <input id="password" name="password" type="password" required autocomplete="current-password"
                       class="mt-1 w-full rounded-lg border border-zinc-300 px-3 py-2 text-zinc-900 shadow-sm focus:border-amber-800 focus:ring-amber-800/20">
            </div>
            <label class="flex items-center gap-2 text-sm text-zinc-600">
                <input type="checkbox" name="remember" value="1" class="rounded border-zinc-300 text-amber-900 focus:ring-amber-800">
                {{ __('admin.login.remember') }}
            </label>
            <button type="submit"
                    class="w-full rounded-lg bg-zinc-900 py-2.5 text-sm font-medium text-white hover:bg-zinc-800">
                {{ __('admin.login.sign_in') }}
            </button>
        </form>
    </div>
</body>
</html>
