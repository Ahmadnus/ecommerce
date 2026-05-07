<?php
// app/Http/Requests/Admin/SeoSettingRequest.php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SeoSettingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
         return [

        'seo_title'               => ['array'],
        'seo_title.*'             => ['nullable', 'string', 'max:160'],

        'seo_description'         => ['array'],
        'seo_description.*'       => ['nullable', 'string', 'max:320'],

        'seo_keywords'            => ['array'],
        'seo_keywords.*'          => ['nullable', 'string', 'max:500'],

        'og_title'                => ['array'],
        'og_title.*'              => ['nullable', 'string', 'max:160'],

        'og_description'          => ['array'],
        'og_description.*'        => ['nullable', 'string', 'max:320'],

        'twitter_title'           => ['array'],
        'twitter_title.*'         => ['nullable', 'string', 'max:160'],

        'twitter_description'     => ['array'],
        'twitter_description.*'   => ['nullable', 'string', 'max:320'],

        'canonical_url'           => ['nullable', 'url', 'max:500'],
        'robots'                  => ['nullable', 'string', 'max:100'],
        'twitter_card'            => ['nullable', 'string'],
        'og_type'                 => ['nullable', 'string', 'max:50'],

        'is_active'               => ['boolean'],

        'og_image'                => ['nullable', 'image', 'max:2048'],
        'favicon'                 => ['nullable', 'file', 'mimes:ico,png,svg', 'max:512'],
    ];
    }
}