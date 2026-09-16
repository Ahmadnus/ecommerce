@props([
    'name' => 'quantity',
    'value' => 1,
    'min' => 1,
    'max' => 100,
    'inputId' => null,
])

@php $id = $inputId ?? 'qty-' . \Illuminate\Support\Str::random(6); @endphp

{{-- Self-contained stepper: the buttons drive the number input and then
     dispatch a change event, so any listener bound to the input (cart line
     updates, price recalculation) keeps working unmodified. --}}
<div {{ $attributes->merge(['class' => 'sf-qty']) }}
     x-data="{
        value: {{ (int) $value }},
        min: {{ (int) $min }},
        max: {{ (int) $max }},
        step(delta) {
            const next = Math.min(this.max, Math.max(this.min, this.value + delta));
            if (next === this.value) return;
            this.value = next;
            $refs.input.value = next;
            $refs.input.dispatchEvent(new Event('change', { bubbles: true }));
        },
     }">
    <button type="button" class="sf-qty__btn"
            @click="step(-1)" :disabled="value <= min"
            aria-label="{{ __('app.decrease_quantity') }}">&minus;</button>

    <input type="number"
           class="sf-qty__input"
           id="{{ $id }}"
           name="{{ $name }}"
           x-ref="input"
           x-model.number="value"
           min="{{ $min }}"
           max="{{ $max }}"
           aria-label="{{ __('app.quantity') }}">

    <button type="button" class="sf-qty__btn"
            @click="step(1)" :disabled="value >= max"
            aria-label="{{ __('app.increase_quantity') }}">+</button>
</div>
