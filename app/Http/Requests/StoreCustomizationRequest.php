<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize incoming data before validation.
     * PHP auto-casts numeric array keys ('1','2','4','5','6') to integers
     * when they arrive via $_FILES or $_POST — we cast them back to strings.
     */
    protected function prepareForValidation(): void
    {
        // Cast selected_zones values to strings (fixes robe zones '1','2','4','5','6')
        if ($this->has('selected_zones')) {
            $this->merge([
                'selected_zones' => array_map('strval', (array) $this->input('selected_zones', [])),
            ]);
        }

        // Cast texts keys to strings
        if ($this->has('texts')) {
            $raw = (array) $this->input('texts', []);
            $this->merge([
                'texts' => array_combine(
                    array_map('strval', array_keys($raw)),
                    array_values($raw)
                ),
            ]);
        }

        // Cast text_styles keys to strings
        if ($this->has('text_styles')) {
            $raw = (array) $this->input('text_styles', []);
            $this->merge([
                'text_styles' => array_combine(
                    array_map('strval', array_keys($raw)),
                    array_values($raw)
                ),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            // Colors — hex strings
            'colors'            => ['nullable', 'array'],
            'colors.*'          => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{3,6}$/'],

            // Per-zone text (keys may be numeric strings for robe: '1','2','4','5','6')
            'texts'             => ['nullable', 'array'],
            'texts.*'           => ['nullable', 'string', 'max:100'],

            // Text style metadata per zone
            'text_styles'           => ['nullable', 'array'],
            'text_styles.*'         => ['nullable', 'array'],
            'text_styles.*.color'   => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{3,6}$/'],
            'text_styles.*.fontSize'=> ['nullable', 'integer', 'min:8', 'max:120'],
            'text_styles.*.fontStyle'=> ['nullable', 'string', 'in:normal,italic'],

            // Zone selection — accepts both alpha ('A','G') and numeric ('1','4') keys
            'selected_zones'    => ['nullable', 'array'],
            'selected_zones.*'  => ['nullable', 'string', 'max:8'],

            // Size — validated against the garment's valid sizes in the controller
            'size'              => ['nullable', 'string', 'max:10'],

            // Image uploads — images[A], images[1], images[G], etc.
            'images'            => ['nullable', 'array'],
            'images.*'          => [
                'nullable',
                'file',
                'mimes:jpeg,jpg,png,gif,webp,svg',
                'max:10240',
            ],

            // Design snapshot JSON
            'design_snapshot'   => ['nullable', 'string'],

            // Customer notes
            'notes'             => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'colors.*.regex'        => 'يجب أن يكون اللون بصيغة HEX صحيحة (مثال: #FF0000)',
            'texts.*.max'           => 'النص في كل منطقة يجب ألا يتجاوز 100 حرف',
            'images.*.mimes'        => 'يُقبل فقط: JPEG، PNG، GIF، WebP، SVG',
            'images.*.max'          => 'الحد الأقصى لحجم الملف 10 ميغابايت',
            'notes.max'             => 'الملاحظات يجب ألا تتجاوز 1000 حرف',
        ];
    }
}