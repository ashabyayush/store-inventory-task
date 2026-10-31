<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        return Product::with('category')
            ->when($request->category_id, fn ($q, $id) => $q->where('category_id', $id))
            ->orderBy('name')
            ->get();
    }

    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        return response()->json($product->load('category'), 201);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return response()->json($product->load('category'));
    }

    public function destroy(Product $product)
    {
        // order_items-la irundha delete panna koodadhu (order history udaindhidum)
        if ($product->orderItems()->exists()) {
            return response()->json([
                'message' => 'This product is part of past orders, so it cannot be deleted. Set its stock to 0 instead.',
            ], 422);
        }

        $product->delete();

        return response()->noContent();
    }

    public function lowStock(Request $request)
    {
        $threshold = (int) $request->query('threshold', config('inventory.low_stock_threshold'));

        return response()->json([
            'threshold' => $threshold,
            'products' => Product::with('category')
                ->where('stock', '<', $threshold)
                ->orderBy('stock')
                ->get(),
        ]);
    }
}