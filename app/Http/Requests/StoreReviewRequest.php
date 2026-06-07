<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:10', 'max:1000'],
            'reviewer_email' => ['nullable', 'email', 'max:255'],
        ];

        // Only require name if the user is a guest
        if (!auth()->check()) {
            $rules['reviewer_name'] = ['required', 'string', 'max:100'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'rating.required'        => 'يرجى اختيار تقييم بالنجوم.',
            'rating.min'             => 'أقل تقييم هو نجمة واحدة.',
            'rating.max'             => 'أعلى تقييم هو 5 نجوم.',
            'rating.integer'         => 'التقييم يجب أن يكون رقماً.',
            'comment.required'       => 'يرجى كتابة تعليق.',
            'comment.min'            => 'التعليق يجب أن يكون 10 أحرف على الأقل.',
            'comment.max'            => 'التعليق يجب ألا يتجاوز 1000 حرف.',
            'reviewer_name.required' => 'يرجى إدخال اسمك.',
        ];
    }

    // Cast rating to integer before validation
    protected function prepareForValidation(): void
    {
        if ($this->has('rating')) {
            $this->merge(['rating' => (int) $this->rating]);
        }
    }
}