@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <h1 class="font-display text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

    {{-- Validation errors --}}
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl p-4 mb-6 text-sm">
        <p class="font-semibold mb-2">Please fix the following:</p>
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('checkout.place') }}" id="checkout-form">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

            {{-- ── LEFT: Form fields ──────────────────────────────────────── --}}
            <div class="lg:col-span-3 space-y-8">

                {{-- Contact Info --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="font-semibold text-gray-900 text-lg mb-5 flex items-center gap-2">
                        <span class="w-7 h-7 bg-brand-600 text-white text-xs font-bold rounded-full flex items-center justify-center">1</span>
                        Contact Information
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   placeholder="Jane Smith"
                                   class="w-full border @error('name') border-red-400 @else border-gray-200 @enderror rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   placeholder="jane@example.com"
                                   class="w-full border @error('email') border-red-400 @else border-gray-200 @enderror rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition">
                            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}"
                                   placeholder="+1 (555) 000-0000"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition">
                        </div>
                    </div>
                </div>

                {{-- Shipping Address --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="font-semibold text-gray-900 text-lg mb-5 flex items-center gap-2">
                        <span class="w-7 h-7 bg-brand-600 text-white text-xs font-bold rounded-full flex items-center justify-center">2</span>
                        Shipping Address
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Street Address *</label>
                            <input type="text" name="address" value="{{ old('address') }}" required
                                   placeholder="123 Main Street, Apt 4B"
                                   class="w-full border @error('address') border-red-400 @else border-gray-200 @enderror rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition">
                            @error('address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">City *</label>
                            <input type="text" name="city" value="{{ old('city') }}" required
                                   placeholder="New York"
                                   class="w-full border @error('city') border-red-400 @else border-gray-200 @enderror rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition">
                            @error('city')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">State / Province</label>
                            <input type="text" name="state" value="{{ old('state') }}"
                                   placeholder="NY"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">ZIP / Postal Code *</label>
                            <input type="text" name="zip" value="{{ old('zip') }}" required
                                   placeholder="10001"
                                   class="w-full border @error('zip') border-red-400 @else border-gray-200 @enderror rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition">
                            @error('zip')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Country *</label>
                            <select name="country" required
                                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition">
                                <option value="US" {{ old('country', 'US') === 'US' ? 'selected' : '' }}>United States</option>
                                <option value="GB" {{ old('country') === 'GB' ? 'selected' : '' }}>United Kingdom</option>
                                <option value="CA" {{ old('country') === 'CA' ? 'selected' : '' }}>Canada</option>
                                <option value="AU" {{ old('country') === 'AU' ? 'selected' : '' }}>Australia</option>
                                <option value="DE" {{ old('country') === 'DE' ? 'selected' : '' }}>Germany</option>
                                <option value="NL" {{ old('country') === 'NL' ? 'selected' : '' }}>Netherlands</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Payment --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="font-semibold text-gray-900 text-lg mb-5 flex items-center gap-2">
                        <span class="w-7 h-7 bg-brand-600 text-white text-xs font-bold rounded-full flex items-center justify-center">3</span>
                        Payment Method
                    </h2>

                    {{-- Method selector --}}
                    <div class="grid grid-cols-3 gap-3 mb-6">
                        @foreach([['card','💳','Credit Card'], ['paypal','🅿️','PayPal'], ['bank_transfer','🏦','Bank Transfer']] as [$val, $icon, $label])
                        <label class="relative cursor-pointer">
                            <input type="radio" name="payment_method" value="{{ $val }}"
                                   {{ old('payment_method', 'card') === $val ? 'checked' : '' }}
                                   class="peer sr-only" onchange="toggleCardFields()">
                            <div class="border-2 border-gray-200 peer-checked:border-brand-500 peer-checked:bg-brand-50 rounded-xl p-3 text-center transition-all">
                                <div class="text-2xl mb-1">{{ $icon }}</div>
                                <div class="text-xs font-medium text-gray-700">{{ $label }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    {{-- Card fields (shown only for 'card') --}}
                    <div id="card-fields" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Card Number</label>
                            <input type="text" name="card_number" value="{{ old('card_number') }}"
                                   placeholder="1234 5678 9012 3456" maxlength="19"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition font-mono"
                                   oninput="formatCard(this)">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Expiry Date</label>
                                <input type="text" name="card_expiry" value="{{ old('card_expiry') }}"
                                       placeholder="MM / YY" maxlength="7"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition font-mono"
                                       oninput="formatExpiry(this)">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">CVV</label>
                                <input type="text" name="card_cvv" value="{{ old('card_cvv') }}"
                                       placeholder="123" maxlength="4"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition font-mono">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Order Notes --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Order Notes (optional)</label>
                    <textarea name="notes" rows="3" placeholder="Any special instructions for your order..."
                              class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition resize-none">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- ── RIGHT: Order Summary ────────────────────────────────────── --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl border border-gray-100 p-6 sticky top-20">
                    <h2 class="font-semibold text-gray-900 text-lg mb-5">Order Summary</h2>

                    {{-- Cart items --}}
                    <div class="space-y-3 mb-5">
                        @foreach($summary['items'] as $productId => $item)
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-50 flex-shrink-0 relative">
                                <img src="{{ $item['image'] ?? 'https://picsum.photos/seed/'.$productId.'/100/100' }}"
                                     alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                <span class="absolute -top-1 -right-1 w-5 h-5 bg-brand-600 text-white text-xs font-bold rounded-full flex items-center justify-center">
                                    {{ $item['quantity'] }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 line-clamp-1">{{ $item['name'] }}</p>
                                <p class="text-xs text-gray-500">${{ number_format($item['price'], 2) }} × {{ $item['quantity'] }}</p>
                            </div>
                            <p class="text-sm font-semibold">${{ number_format($item['subtotal'], 2) }}</p>
                        </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-100 pt-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Subtotal</span>
                            <span>${{ number_format($summary['subtotal'], 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Tax (10%)</span>
                            <span>${{ number_format($summary['tax'], 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Shipping</span>
                            <span class="{{ $summary['shipping'] == 0 ? 'text-green-600 font-medium' : '' }}">
                                {{ $summary['shipping'] == 0 ? 'Free' : '$' . number_format($summary['shipping'], 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 mt-4 pt-4 flex justify-between items-center mb-6">
                        <span class="font-bold text-gray-900">Total</span>
                        <span class="font-bold text-2xl text-gray-900">${{ number_format($summary['total'], 2) }}</span>
                    </div>

                    <button type="submit" id="place-order-btn"
                            class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-4 rounded-xl transition-colors flex items-center justify-center gap-2 text-base">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Place Order
                    </button>
                    <p class="text-xs text-gray-400 text-center mt-3">
                        🔒 Your payment info is encrypted and secure
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Show/hide card fields based on payment method
    function toggleCardFields() {
        const method = document.querySelector('input[name="payment_method"]:checked')?.value;
        document.getElementById('card-fields').style.display = method === 'card' ? 'block' : 'none';
    }

    // Auto-format card number: 1234 5678 ...
    function formatCard(input) {
        let v = input.value.replace(/\D/g, '').substring(0, 16);
        input.value = v.match(/.{1,4}/g)?.join(' ') || v;
    }

    // Auto-format expiry: MM / YY
    function formatExpiry(input) {
        let v = input.value.replace(/\D/g, '').substring(0, 4);
        if (v.length >= 2) v = v.substring(0,2) + ' / ' + v.substring(2);
        input.value = v;
    }

    // Disable submit button on form submit to prevent double-clicks
    document.getElementById('checkout-form').addEventListener('submit', function() {
        const btn = document.getElementById('place-order-btn');
        btn.disabled   = true;
        btn.innerHTML  = '<svg class="spinner w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg> Processing...';
    });

    // Initial state
    toggleCardFields();
</script>
@endpush
