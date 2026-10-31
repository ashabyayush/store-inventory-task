<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // All orders (paginated). ?email= filters by customer email.
    public function index(Request $request)
    {
        $orders = Order::with('customer', 'items.product')
            ->when($request->query('email'), fn ($q, $email) => $q->whereHas(
                'customer',
                fn ($c) => $c->where('email', 'like', '%' . $email . '%')
            ))
            ->latest('id')
            ->paginate(10);

        return response()->json($orders);
    }

    public function store(StoreOrderRequest $request, OrderService $service)
    {
        return response()->json($service->create($request->validated()), 201);
    }
}