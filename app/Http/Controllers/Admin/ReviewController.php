<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Routes to add inside your admin middleware group in web.php:
 *
 *   Route::get( 'reviews',                    [ReviewController::class, 'index'])  ->name('reviews.index');
 *   Route::get( 'reviews/{review}',           [ReviewController::class, 'show'])   ->name('reviews.show');
 *   Route::patch('reviews/{review}/approve',  [ReviewController::class, 'approve'])->name('reviews.approve');
 *   Route::patch('reviews/{review}/reject',   [ReviewController::class, 'reject']) ->name('reviews.reject');
 *   Route::patch('reviews/{review}/pin',      [ReviewController::class, 'pin'])    ->name('reviews.pin');
 *   Route::delete('reviews/{review}',         [ReviewController::class, 'destroy'])->name('reviews.destroy');
 *   Route::get( 'products/{product}/reviews', [ReviewController::class, 'forProduct'])->name('products.reviews');
 */
class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $query = ProductReview::with(['product', 'user'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->paginate(25)->withQueryString();

        $stats = [
            'total'    => ProductReview::count(),
            'pending'  => ProductReview::where('status', 'pending')->count(),
            'approved' => ProductReview::where('status', 'approved')->count(),
            'rejected' => ProductReview::where('status', 'rejected')->count(),
        ];

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    public function show(ProductReview $review): View
    {
        $review->load(['product', 'user']);
        return view('admin.reviews.show', compact('review'));
    }

    public function forProduct(Product $product): View
    {
        $reviews = $product->reviews()
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.reviews.for-product', compact('product', 'reviews'));
    }

    public function approve(ProductReview $review): RedirectResponse
    {
        $review->update(['status' => 'approved']);
        return back()->with('success', 'تم اعتماد التقييم.');
    }

    public function reject(ProductReview $review): RedirectResponse
    {
        $review->update(['status' => 'rejected']);
        return back()->with('success', 'تم رفض التقييم.');
    }

    public function pin(ProductReview $review): RedirectResponse
    {
        $review->update(['is_pinned' => ! $review->is_pinned]);
        $label = $review->is_pinned ? 'تم تثبيت التقييم.' : 'تم إلغاء تثبيت التقييم.';
        return back()->with('success', $label);
    }

    public function destroy(ProductReview $review): RedirectResponse
    {
        $review->delete();
        return back()->with('success', 'تم حذف التقييم.');
    }
}