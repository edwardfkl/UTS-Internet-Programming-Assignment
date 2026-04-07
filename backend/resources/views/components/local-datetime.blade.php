@props([
    'at' => null,
    /** @var bool Show time with seconds in locale formatting */
    'withSeconds' => false,
    'fallback' => '—',
])

@if ($at)
    @php
        $utc = $at->clone()->utc();
        $iso = $utc->format('Y-m-d\TH:i:s').'Z';
    @endphp
    <time
        class="local-datetime"
        datetime="{{ $iso }}"
        @if ($withSeconds) data-time-style="medium" @endif
    >{{ $utc->format($withSeconds ? 'Y-m-d H:i:s' : 'Y-m-d H:i') }}</time>
@else
    {{ $fallback }}
@endif
