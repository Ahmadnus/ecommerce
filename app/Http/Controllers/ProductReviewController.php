<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Services\ProductReviewService;
use Illuminate\Http\RedirectResponse;

class ProductReviewController extends Controller
{
    public function __construct(
        private readonly ProductReviewService $reviews,
    ) {}

    public function store(StoreReviewRequest $request, string $slug): RedirectResponse
    {
        // Find product manually by slug — avoids route binding issues
        $product = $this->reviews->findActiveProduct($slug);

        $user = auth()->user();

        $error = $this->reviews->submitReview(
            $product,
            $user,
            $request->only(['reviewer_name', 'reviewer_email', 'rating', 'comment']),
            $request->ip()
        );

        if ($error !== null) {
            // Rate-limit rejection keeps the old input like before
            if (str_contains($error, 'تجاوزت الحد')) {
                return back()->withInput()->with('review_error', $error);
            }

            return back()->with('review_error', $error);
        }

        return back()->with('review_success', 'شكراً! سيتم نشر تقييمك بعد المراجعة.');
    }
}
