<?php

namespace App\Exceptions;

use App\Models\Product;
use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(public Product $product, public int $requested)
    {
        parent::__construct(
            "Insufficient stock for {$product->name}: requested {$requested}, available {$product->stock}."
        );
    }

    public function render()
    {
        return response()->json([
            'message' => $this->getMessage(),
            'product_id' => $this->product->id,
            'available' => $this->product->stock,
        ], 422);
    }
}