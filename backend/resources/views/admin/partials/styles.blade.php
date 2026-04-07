@if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@else
    {{-- Vite not built: still load admin UI (run `npm run build` in backend/ for local assets). --}}
    <script src="https://cdn.tailwindcss.com"></script>
@endif
