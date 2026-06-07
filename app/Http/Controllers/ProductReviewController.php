<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;

class ProductReviewController extends Controller
{
    public function store(StoreReviewRequest $request, string $slug): RedirectResponse
    {
        // Find product manually by slug — avoids route binding issues
        $product = Product::where('slug', $slug)->where('status', 'active')->firstOrFail();

        $user = auth()->user();

        // Rate limiting — max 5 per IP per hour
        $key = 'review:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withInput()
                ->with('review_error', 'لقد تجاوزت الحد المسموح به. يرجى المحاولة لاحقاً.');
        }
        RateLimiter::hit($key, 3600);

        // Prevent duplicate for logged-in users
        if ($user) {
            if (ProductReview::where('product_id', $product->id)->where('user_id', $user->id)->exists()) {
                return back()->with('review_error', 'لقد قمت بتقييم هذا المنتج مسبقاً.');
            }
        }

        // Prevent duplicate for guests by email
        if (!$user && $request->filled('reviewer_email')) {
            if (ProductReview::where('product_id', $product->id)->where('reviewer_email', $request->reviewer_email)->exists()) {
                return back()->with('review_error', 'تم استلام تقييمك لهذا المنتج مسبقاً.');
            }
        }

        ProductReview::create([
            'product_id'     => $product->id,
            'user_id'        => $user?->id,
            'reviewer_name'  => $user ? $user->name : $request->reviewer_name,
            'reviewer_email' => $user ? $user->email : $request->reviewer_email,
            'rating'         => (int) $request->rating,
            'comment'        => $request->comment,
            'status'         => 'pending',
        ]);

        return back()->with('review_success', 'شكراً! سيتم نشر تقييمك بعد المراجعة.');
    }
}