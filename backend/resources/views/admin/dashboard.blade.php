@extends('admin.layout')

@section('title', __('admin.dashboard.title'))

@section('content')
    <h1 class="text-2xl font-semibold text-zinc-900">{{ __('admin.dashboard.heading') }}</h1>
    <p class="mt-2 text-sm text-zinc-600">{{ __('admin.dashboard.intro') }}</p>

    <dl class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
            <dt class="text-sm font-medium text-zinc-600">{{ __('admin.dashboard.users') }}</dt>
            <dd class="mt-1 text-3xl font-semibold tabular-nums text-zinc-900">{{ $userCount }}</dd>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
            <dt class="text-sm font-medium text-zinc-600">{{ __('admin.dashboard.products') }}</dt>
            <dd class="mt-1 text-3xl font-semibold tabular-nums text-zinc-900">{{ $productCount }}</dd>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
            <dt class="text-sm font-medium text-zinc-600">{{ __('admin.dashboard.orders_all') }}</dt>
            <dd class="mt-1 text-3xl font-semibold tabular-nums text-zinc-900">{{ $orderCount }}</dd>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
            <dt class="text-sm font-medium text-zinc-600">{{ __('admin.dashboard.awaiting_payment') }}</dt>
            <dd class="mt-1 text-3xl font-semibold tabular-nums text-amber-950">{{ $pendingPaymentCount }}</dd>
        </div>
    </dl>
@endsection
