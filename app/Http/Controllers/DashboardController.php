<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;

class DashboardController extends Controller
{
    public function index()
    {
        $threshold = config('inventory.low_stock_threshold');

        return view('pages.dashboard', [
            'threshold' => $threshold,
            'symbol' => Setting::symbol(),
            'stats' => [
                'orders_today' => Order::whereDate('created_at', today())->count(),
                'revenue_today' => Order::whereDate('created_at', today())->sum('grand_total'),
                'products' => Product::count(),
                'categories' => Category::count(),
                'customers' => Customer::count(),
                'low_stock' => Product::where('stock', '<', $threshold)->count(),
            ],
            'recentOrders' => Order::with('customer')->latest('id')->limit(5)->get(),
            'lowStock' => Product::with('category')
                ->where('stock', '<', $threshold)->orderBy('stock')->limit(5)->get(),
        ]);
    }
}