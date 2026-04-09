<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index() {
        $products = Product::with('category')->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create() {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

   

    public function edit(Product $product) {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

   public function store(Request $request) {
        $data = $request->validate([
            'name'           => 'required|max:255',
            'price'          => 'required|numeric|min:0',
            'sale_price'     => 'nullable|numeric|min:0|lt:price',
            'weight'         => 'nullable|numeric',
            'category_id'    => 'required|exists:categories,id',
            'stock_quantity' => 'required|integer',
            'main_image'     => 'required|image',
            // لا نضع is_active هنا في الـ validate كـ required لأنها checkbox
        ]);

        $data['slug'] = Str::slug($request->name);

        // ─── الإضافة هنا: تحويل حالة الـ Checkbox إلى قيمة منطقية ───
        $data['is_active']   = $request->has('is_active');   // إذا موجود يعني 1، غير موجود يعني 0
        $data['is_featured'] = $request->has('is_featured'); // إذا موجود يعني 1، غير موجود يعني 0

        $product = Product::create($data);

        if ($request->hasFile('main_image')) {
            $product->addMediaFromRequest('main_image')->toMediaCollection('products');
        }

        return redirect()->route('admin.products.index')->with('success', 'تم إنشاء المنتج بنجاح');
    }

    public function update(Request $request, Product $product) {
        $data = $request->validate([
            'name'           => 'required|max:255',
            'price'          => 'required|numeric|min:0',
            'sale_price'     => 'nullable|numeric|min:0|lt:price',
            'category_id'    => 'required|exists:categories,id',
            'stock_quantity' => 'required|integer',
            'main_image'     => 'nullable|image',
        ]);

        if ($request->name !== $product->name) {
            $data['slug'] = Str::slug($request->name);
        }
        
        // ─── الإضافة هنا أيضاً لضمان التحديث ───
        $data['is_active']   = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');

        $product->update($data);

        if ($request->hasFile('main_image')) {
            $product->clearMediaCollection('products');
            $product->addMediaFromRequest('main_image')->toMediaCollection('products');
        }

        return redirect()->route('admin.products.index')->with('success', 'تم تحديث المنتج بنجاح');
    }

    public function destroy(Product $product) {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'تم نقل المنتج إلى سلة المهملات');
    }
}