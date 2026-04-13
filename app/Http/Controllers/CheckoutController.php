<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Exception;

class CheckoutController extends Controller
{
    // حقن خدمة السلة عبر الـ Constructor
    public function __construct(private readonly CartService $cart) {}

    /**
     * عرض صفحة السلة الموحدة مع فورم بيانات الشحن
     */
    public function index(): View|RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')
                             ->with('error', 'سلة التسوق فارغة، أضف بعض المنتجات أولاً.');
        }

        $summary = $this->cart->getSummary();
        $user    = Auth::user();

        return view('cart.checkout', compact('summary', 'user'));
    }

    /**
     * معالجة إرسال الطلب، خصم المخزون، وتفريغ السلة
     */
   public function placeOrder(Request $request): RedirectResponse
{
    if ($this->cart->isEmpty()) {
        return redirect()->route('cart.index')
                         ->with('error', 'سلة التسوق فارغة.');
    }

    $validated = $request->validate([
        'shipping_name'    => 'required|string|max:255',
        'shipping_phone'   => ['required', 'string'],
        'shipping_address' => 'required|string|max:500',
        'shipping_city'    => 'required|string|max:100',
        'shipping_zip'     => 'nullable|string|max:20',
        'notes'            => 'nullable|string|max:1000',
    ]);

    $summary = $this->cart->getSummary();

    try {
        $order = DB::transaction(function () use ($validated, $summary) {

            // ✅ Use ->id directly — never Auth::id() with phone-based auth
            $userId = Auth::check() ? Auth::user()->id : null;

            $order = Order::create([
                'user_id'          => $userId,
                'order_number'     => Order::generateOrderNumber(),
                'status'           => Order::STATUS_PENDING,
                'payment_method'   => Order::PAYMENT_COD,
                'payment_status'   => Order::PAYMENT_PENDING,
                'subtotal'         => (float) $summary['subtotal'],
                'tax_amount'       => (float) ($summary['tax'] ?? 0),
                'shipping_amount'  => (float) ($summary['shipping'] ?? 0),
                'total_amount'     => (float) $summary['total'],
                'shipping_name'    => $validated['shipping_name'],
                'shipping_phone'   => $validated['shipping_phone'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_city'    => $validated['shipping_city'],
                'shipping_zip'     => $validated['shipping_zip'] ?? null,
                'notes'            => $validated['notes'] ?? null,
            ]);

            foreach ($summary['items'] as $item) {
                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_id'         => $item['product_id'],
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'product_name'       => $item['name'],
                    'quantity'           => $item['quantity'],
                    'unit_price'         => $item['price'],
                    'total_price'        => $item['subtotal'],
                ]);

                if (!empty($item['variant_id'])) {
                    $variant = ProductVariant::lockForUpdate()->find($item['variant_id']);
                } else {
                    $variant = ProductVariant::where('product_id', $item['product_id'])
                                             ->lockForUpdate()->first();
                }

                if (!$variant || $variant->stock_quantity < $item['quantity']) {
                    throw new \Exception("المخزون غير كافٍ للمنتج: " . $item['name']);
                }

                $variant->decrement('stock_quantity', $item['quantity']);
            }

            return $order;
        });

        $this->cart->clear();

       return redirect()
    ->route('orders.success', ['orderNumber' => $order->order_number])
    ->with('success', 'تم إرسال طلبك بنجاح!');

    } catch (\Exception $e) {
        return redirect()->back()->withInput()->with('error', $e->getMessage());
    }
}
}