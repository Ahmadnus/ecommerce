<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use App\Services\ReviewAdminService;
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
    public function __construct(
        private readonly ReviewAdminService $reviews,
    ) {}

    public function index(Request $request): View
    {
        $data = $this->reviews->getIndexData(
            $request->only(['status', 'product_id', 'rating'])
        );

        return view('admin.reviews.index', $data);
    }

    public function show(ProductReview $review): View
    {
        $review->load(['product', 'user']);
        return view('admin.reviews.show', compact('review'));
    }

    public function forProduct(Product $product): View
    {
        $reviews = $this->reviews->getReviewsForProduct($product);

        return view('admin.reviews.for-product', compact('product', 'reviews'));
    }

    public function approve(ProductReview $review): RedirectResponse
    {
        $this->reviews->approve($review);
        return back()->with('success', 'تم اعتماد التقييم.');
    }

    public function reject(ProductReview $review): RedirectResponse
    {
        $this->reviews->reject($review);
        return back()->with('success', 'تم رفض التقييم.');
    }

    public function pin(ProductReview $review): RedirectResponse
    {
        $pinned = $this->reviews->togglePin($review);
        $label = $pinned ? 'تم تثبيت التقييم.' : 'تم إلغاء تثبيت التقييم.';
        return back()->with('success', $label);
    }

    public function destroy(ProductReview $review): RedirectResponse
    {
        $this->reviews->delete($review);
        return back()->with('success', 'تم حذف التقييم.');
    }
}
