<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Jobs\SendOrderConfirmation;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function create(array $data): Order
    {
        $order = DB::transaction(function () use ($data) {
            // Same product multiple lines-la vandha merge pannu
            $lines = collect($data['items'])
                ->groupBy('product_id')
                ->map(fn ($group) => (int) $group->sum('quantity'));

            // Row lock (ordered by id => deadlock varaadhu). Idhu dhaan oversell-a thadukkum.
            $products = Product::whereIn('id', $lines->keys())
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($lines as $id => $qty) {
                if ($products[$id]->stock < $qty) {
                    throw new InsufficientStockException($products[$id], $qty);
                }
            }

            $customer = Customer::firstOrCreate(
                ['email' => $data['customer_email']],
                ['name' => $data['customer_name']]
            );

            $order = Order::create(['customer_id' => $customer->id]);

            $subtotal = 0;
            $taxTotal = 0;

            foreach ($lines as $id => $qty) {
                $product = $products[$id];

                $lineSubtotal = round($product->price * $qty, 2);
                $lineTax = round($lineSubtotal * $product->tax_percent / 100, 2);

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $product->price,
                    'tax_percent' => $product->tax_percent,
                    'tax_amount' => $lineTax,
                    'line_total' => $lineSubtotal + $lineTax,
                ]);

                $product->decrement('stock', $qty);

                $subtotal += $lineSubtotal;
                $taxTotal += $lineTax;
            }

            $order->update([
                'order_number' => sprintf('ORD-%06d', $order->id),
                'subtotal' => $subtotal,
                'tax_total' => $taxTotal,
                'grand_total' => $subtotal + $taxTotal,
            ]);

            return $order;
        });

        // Transaction commit aana apram dhaan queue-ku anuppurom
        SendOrderConfirmation::dispatch($order);

        return $order->load('customer', 'items.product');
    }
}