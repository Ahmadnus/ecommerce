<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * CheckoutRequest
 *
 * Validates all checkout form fields before they reach the controller/service.
 * This keeps validation logic out of controllers entirely.
 */
class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Open to guests and authenticated users
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:100'],
            'email'          => ['required', 'email', 'max:150'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'address'        => ['required', 'string', 'max:255'],
            'city'           => ['required', 'string', 'max:100'],
            'state'          => ['nullable', 'string', 'max:100'],
            'zip'            => ['required', 'string', 'max:20'],
            'country'        => ['required', 'string', 'size:2'],
            'payment_method' => ['required', 'in:card,paypal,bank_transfer'],
            'notes'          => ['nullable', 'string', 'max:500'],
            // Card fields (only required if payment_method is 'card')
            'card_number'    => ['required_if:payment_method,card', 'nullable', 'string'],
            'card_expiry'    => ['required_if:payment_method,card', 'nullable', 'string'],
            'card_cvv'       => ['required_if:payment_method,card', 'nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'Full name is required.',
            'email.required'   => 'Email address is required.',
            'email.email'      => 'Please enter a valid email address.',
            'address.required' => 'Street address is required.',
            'city.required'    => 'City is required.',
            'zip.required'     => 'ZIP / Postal code is required.',
            'country.required' => 'Country is required.',
            'country.size'     => 'Country must be a 2-letter code (e.g. US).',
        ];
    }
}
