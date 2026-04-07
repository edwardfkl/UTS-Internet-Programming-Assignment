<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderItemController extends Controller
{
    public function edit(OrderItem $orderItem): View
    {
        $orderItem->load(['order', 'product']);
        $products = Product::query()->orderBy('name')->get();

        return view('admin.order-items.edit', compact('orderItem', 'products'));
    }

    public function update(Request $request, OrderItem $orderItem): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
                Rule::unique('order_items', 'product_id')
                    ->where(fn ($q) => $q->where('order_id', $orderItem->order_id))
                    ->ignore($orderItem->id),
            ],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $orderItem->fill($data);
        $orderItem->save();

        return redirect()
            ->route('admin.orders.show', $orderItem->order)
            ->with('success', 'Line item updated.');
    }

    public function destroy(OrderItem $orderItem): RedirectResponse
    {
        $order = $orderItem->order;
        $orderItem->delete();

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Line item removed.');
    }
}
