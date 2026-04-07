@php
    use App\Support\AdminLocales;
@endphp
<div class="relative" data-lang-menu>
    <button type="button"
            class="flex h-9 w-9 items-center justify-center rounded-lg text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 focus:outline-none focus:ring-2 focus:ring-amber-800/30"
            aria-expanded="false"
            aria-haspopup="true"
            aria-controls="admin-lang-menu"
            data-lang-menu-trigger
            title="{{ __('admin.lang.menu') }}">
        <span class="sr-only">{{ __('admin.lang.menu') }}</span>
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253M12 3v18"/>
        </svg>
    </button>
    <div id="admin-lang-menu"
         role="menu"
         hidden
         data-lang-menu-panel
         class="absolute right-0 z-50 mt-2 min-w-[11rem] rounded-xl border border-zinc-200 bg-white py-1 shadow-lg">
        @foreach (AdminLocales::ALLOWED as $loc)
            <form method="post" action="{{ route('admin.locale') }}" role="none">
                @csrf
                <input type="hidden" name="locale" value="{{ $loc }}">
                <button type="submit"
                        role="menuitem"
                        class="flex w-full px-4 py-2.5 text-left text-sm text-zinc-800 hover:bg-zinc-50 @if (app()->getLocale() === $loc) font-semibold text-amber-950 @endif">
                    {{ __('admin.lang.names.'.$loc) }}
                </button>
            </form>
        @endforeach
    </div>
</div>
<script>
    (function () {
        var root = document.querySelector('[data-lang-menu]');
        if (!root) return;
        var btn = root.querySelector('[data-lang-menu-trigger]');
        var panel = root.querySelector('[data-lang-menu-panel]');
        if (!btn || !panel) return;
        function close() {
            panel.hidden = true;
            btn.setAttribute('aria-expanded', 'false');
        }
        function open() {
            panel.hidden = false;
            btn.setAttribute('aria-expanded', 'true');
        }
        function toggle() {
            if (panel.hidden) open(); else close();
        }
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            toggle();
        });
        document.addEventListener('click', function () {
            close();
        });
        root.addEventListener('click', function (e) {
            e.stopPropagation();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') close();
        });
    })();
</script>
