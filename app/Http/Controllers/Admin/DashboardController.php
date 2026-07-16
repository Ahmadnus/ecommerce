<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $products = Product::with(['categories', 'media'])
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'products_count'   => Product::count(),
            'orders_count'     => class_exists(Order::class) ? Order::count() : 0,
            'categories_count' => Category::count(),
            'users_count'      => User::count(),
        ];

        return view('admin.dashboard', compact('products', 'stats'));
    }
}