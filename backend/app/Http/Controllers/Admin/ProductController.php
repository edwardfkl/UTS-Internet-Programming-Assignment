<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Support\AdminListRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $q = AdminListRequest::search($request);
        [$sort, $dir] = AdminListRequest::sort(
            $request,
            ['id', 'name', 'price', 'stock', 'created_at', 'updated_at'],
            'id',
            'desc',
        );

        $query = Product::query();
        if ($q !== null) {
            $like = '%'.$q.'%';
            $query->where(function ($sub) use ($like) {
                $sub->where('name', 'like', $like)
                    ->orWhere('description', 'like', $like);
            });
        }
        $query->orderBy($sort, $dir);

        $products = $query->paginate(20)->withQueryString();

        return view('admin.products.index', compact('products', 'sort', 'dir', 'q'));
    }

    public function create(): View
    {
        return view('admin.products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedProduct($request);
        Product::query()->create($data);

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->fill($this->validatedProduct($request));
        $product->save();

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $locked = OrderItem::query()
            ->where('product_id', $product->id)
            ->whereHas('order', fn ($q) => $q->where('status', '!=', Order::STATUS_CART))
            ->exists();

        if ($locked) {
            return back()->withErrors([
                'delete' => 'Cannot delete a product that appears on placed or pending orders.',
            ]);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    /**
     * @return array{name: string, description: string|null, price: string, image_url: string|null, stock: int}
     */
    private function validatedProduct(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'stock' => ['required', 'integer', 'min:0', 'max:2147483647'],
        ]);

        $validated['description'] = $validated['description'] ?: null;
        $validated['image_url'] = $validated['image_url'] ?: null;

        return $validated;
    }
}
