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
    // 1. التحقق من أن السلة ليست فارغة
    if ($this->cart->isEmpty()) {
        return redirect()->route('cart.index')
                         ->with('error', 'سلة التسوق فارغة، لا يمكن إتمام الطلب.');
    }

    // 2. التحقق من صحة بيانات فورم الشحن
    $validated = $request->validate([
        'shipping_name'    => 'required|string|max:255',
        'shipping_phone'   => ['required', 'string'],
        'shipping_address' => 'required|string|max:500',
        'shipping_city'    => 'required|string|max:100',
        'shipping_zip'     => 'nullable|string|max:20',
        'notes'            => 'nullable|string|max:1000',
    ], [
        'shipping_name.required'    => 'الاسم الكامل مطلوب.',
        'shipping_phone.required'   => 'رقم الهاتف مطلوب.',
        'shipping_phone.regex'      => 'رقم الهاتف يجب أن يبدأ بـ 05 ويتكون من 10 أرقام.',
        'shipping_address.required' => 'العنوان التفصيلي مطلوب للشحن.',
        'shipping_city.required'    => 'يرجى تحديد المدينة.',
    ]);

    // 3. جلب ملخص السلة
    $summary = $this->cart->getSummary();

    try {
        // 4. تنفيذ العملية كاملة داخل Transaction واحدة
        $order = DB::transaction(function () use ($validated, $summary) {

            // --- أ. إنشاء سجل الطلب الرئيسي ---
            $order = Order::create([
                'user_id'          => Auth::id(),
                'order_number'     => Order::generateOrderNumber(),
                'status'           => Order::STATUS_PENDING,
                'payment_method'   => Order::PAYMENT_COD,
                'payment_status'   => Order::PAYMENT_PENDING,
                'subtotal'         => $summary['subtotal'],
                'tax_amount'       => $summary['tax'] ?? 0,
                'shipping_amount'  => $summary['shipping'] ?? 0,
                'total_amount'     => $summary['total'],
                'shipping_name'    => $validated['shipping_name'],
                'shipping_phone'   => $validated['shipping_phone'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_city'    => $validated['shipping_city'],
                'shipping_zip'     => $validated['shipping_zip'] ?? null,
                'notes'            => $validated['notes'] ?? null,
            ]);

            // --- ب. معالجة العناصر وخصم المخزون ---
            foreach ($summary['items'] as $item) {
                
                // إنشاء عنصر الطلب (Snapshot)
                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_id'         => $item['product_id'],
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'product_name'       => $item['name'],
                    'quantity'           => $item['quantity'],
                    'unit_price'         => $item['price'],
                    'total_price'        => $item['subtotal'],
                ]);

                // --- منطق خصم المخزون الذكي ---
                if (!empty($item['variant_id'])) {
                    // إذا وجد variant_id نخصم منه مع حماية من البيع المتزامن
                    $variant = ProductVariant::lockForUpdate()->find($item['variant_id']);
                    if ($variant && $variant->stock_quantity >= $item['quantity']) {
                        $variant->decrement('stock_quantity', $item['quantity']);
                    } else {
                        throw new \Exception("عذراً، المخزون غير كافٍ للمنتج: " . $item['name']);
                    }
                } 
                else {
                    // إذا كان null (مثل حالتك)، ابحث عن أول Variant للمنتج
                    $variant = ProductVariant::where('product_id', $item['product_id'])
                                              ->lockForUpdate()
                                              ->first();
                    
                    if ($variant && $variant->stock_quantity >= $item['quantity']) {
                        $variant->decrement('stock_quantity', $item['quantity']);
                    } else {
                        throw new \Exception("عذراً، المخزون غير كافٍ للمنتج: " . $item['name']);
                    }
                }
            }

            return $order;
        });

        // 5. تفريغ السلة (فقط بعد نجاح الـ Transaction)
        $this->cart->clear();

        // 6. التوجه لصفحة النجاح
        return redirect()
            ->route('orders.success', $order->order_number)
            ->with('success', 'تم إرسال طلبك بنجاح!');

    } catch (\Exception $e) {
        // في حال فشل أي شيء، يتم التراجع عن كل التغييرات في القاعدة
        return redirect()
            ->back()
            ->withInput()
            ->with('error', $e->getMessage());
    }
}
}