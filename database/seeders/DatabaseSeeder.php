<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $stationery  = Category::create(['name' => 'Stationery']);
        $grocery     = Category::create(['name' => 'Grocery']);
        $electronics = Category::create(['name' => 'Electronics']);

        Product::create(['category_id' => $stationery->id, 'name' => 'Gel Pen (Blue, pack of 5)', 'code' => 'STN-PEN-01', 'price' => 120, 'tax_percent' => 18, 'stock' => 50]);
        Product::create(['category_id' => $grocery->id, 'name' => 'Assam Tea 500 g', 'code' => 'GRC-TEA-500', 'price' => 240, 'tax_percent' => 5, 'stock' => 8]);
        Product::create(['category_id' => $stationery->id, 'name' => 'Notebook A5', 'code' => 'STN-NB-A5', 'price' => 85, 'tax_percent' => 12, 'stock' => 3]);
        Product::create(['category_id' => $electronics->id, 'name' => 'USB Cable 1m', 'code' => 'ELC-USB-1M', 'price' => 199, 'tax_percent' => 18, 'stock' => 25]);
        User::create([
            'name' => 'Store Admin',
            'email' => 'admin@store.test',
            'password' => Hash::make('password'),
        ]);
        Customer::create(['name' => 'Asha Raman', 'email' => 'asha@example.com']);
        Customer::factory()->count(5)->create();
    }
}