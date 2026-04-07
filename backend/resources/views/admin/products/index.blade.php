@extends('admin.layout')

@section('title', __('admin.products.title'))

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-zinc-900">{{ __('admin.products.heading') }}</h1>
            <p class="mt-1 text-sm text-zinc-600">{{ __('admin.products.intro') }}</p>
        </div>
        <a href="{{ route('admin.products.create') }}"
           class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800">{{ __('admin.products.add') }}</a>
    </div>

    <form method="get" action="{{ route('admin.products.index') }}" class="mt-6 flex flex-wrap items-end gap-3">
        <input type="hidden" name="sort" value="{{ $sort }}">
        <input type="hidden" name="dir" value="{{ $dir }}">
        <div class="min-w-[200px] flex-1">
            <label for="products-q" class="mb-1 block text-xs font-medium text-zinc-600">{{ __('admin.common.search') }}</label>
            <input id="products-q" type="search" name="q" value="{{ $q ?? '' }}"
                   placeholder="{{ __('admin.products.search_placeholder') }}"
                   class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-amber-900 focus:outline-none focus:ring-1 focus:ring-amber-900">
        </div>
        <button type="submit"
                class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800">{{ __('admin.common.search_submit') }}</button>
        @if (! empty($q))
            <a href="{{ route('admin.products.index', ['sort' => $sort, 'dir' => $dir]) }}"
               class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-800 hover:bg-zinc-50">{{ __('admin.common.clear') }}</a>
        @endif
    </form>

    <div class="mt-8 overflow-x-auto rounded-xl border border-zinc-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-zinc-200 text-sm">
            <thead class="bg-zinc-50 text-left text-xs font-medium uppercase tracking-wide text-zinc-600">
            <tr>
                <x-admin.sort-th :label="__('admin.products.col_id')" column="id" :sort="$sort" :dir="$dir" route="admin.products.index"/>
                <x-admin.sort-th :label="__('admin.products.col_name')" column="name" :sort="$sort" :dir="$dir" route="admin.products.index"/>
                <x-admin.sort-th :label="__('admin.products.col_price')" column="price" :sort="$sort" :dir="$dir" route="admin.products.index"/>
                <x-admin.sort-th :label="__('admin.products.col_stock')" column="stock" :sort="$sort" :dir="$dir" route="admin.products.index"/>
                <x-admin.sort-th :label="__('admin.products.col_created')" column="created_at" :sort="$sort" :dir="$dir" route="admin.products.index"/>
                <x-admin.sort-th :label="__('admin.products.col_updated')" column="updated_at" :sort="$sort" :dir="$dir" route="admin.products.index"/>
                <th class="px-4 py-3 text-right">{{ __('admin.products.col_actions') }}</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
            @forelse ($products as $product)
                <tr class="hover:bg-zinc-50/80">
                    <td class="whitespace-nowrap px-4 py-3 tabular-nums text-zinc-600">{{ $product->id }}</td>
                    <td class="px-4 py-3 font-medium text-zinc-900">{{ $product->name }}</td>
                    <td class="whitespace-nowrap px-4 py-3 tabular-nums">{{ number_format((float) $product->price, 2) }}</td>
                    <td class="whitespace-nowrap px-4 py-3 tabular-nums">{{ $product->stock }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-zinc-600"><x-local-datetime :at="$product->created_at"/></td>
                    <td class="whitespace-nowrap px-4 py-3 text-zinc-600"><x-local-datetime :at="$product->updated_at"/></td>
                    <td class="whitespace-nowrap px-4 py-3 text-right">
                        <a href="{{ route('admin.products.edit', $product) }}" class="font-medium text-amber-900 hover:underline">{{ __('admin.products.edit_link') }}</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-zinc-500">{{ __('admin.products.empty') }}</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $products->links() }}
    </div>
@endsection
