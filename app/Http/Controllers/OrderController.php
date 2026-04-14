<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Order success / confirmation page.
     * Accessible by the order owner only.
     */
 public function success(string $orderNumber): View
{
    // نبحث عن الطلب برقم الطلب فقط (للسماح للزوار برؤية صفحة النجاح فوراً)
    // أو يمكنك التأكد أن المستخدم هو صاحب الطلب "فقط إذا كان مسجلاً"
    $order = Order::where('order_number', $orderNumber)
                  ->with('items')
                  ->firstOrFail(); // إذا لم يجد الرقم سيظهر 404، تأكد أن الرقم يمر صح

    return view('orders.success', compact('order'));
}
    /**
     * User's order history.
     */
public function index(Request $request): View
{
    $user = $request->user();
    
    // استخدمنا العلاقة مباشرة كما فعلت في البروفايل
    $orders = $user->orders()->latest()->paginate(10);

    return view('orders.index', [
        'orders' => $orders
    ]);
}
}