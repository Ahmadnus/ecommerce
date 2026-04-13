<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;

class MyDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.my-dashboard';

    protected function getViewData(): array
    {
        return [
            'categories' => Category::with('allchildren')->get(), // حسب ما يظهر في الـ row.blade عندك
            'products' => Product::latest()->take(10)->get(),
            'orders' => Order::latest()->take(10)->get(),
        ];
    }
}