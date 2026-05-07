<?php
// app/Http/Requests/Admin/FooterCompanyInfoRequest.php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FooterCompanyInfoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'company_name'            => ['array'],
            'company_name.*'          => ['nullable', 'string', 'max:200'],
            'description'             => ['array'],
            'description.*'           => ['nullable', 'string', 'max:1000'],
            'location'                => ['array'],
            'location.*'              => ['nullable', 'string', 'max:300'],
            'phone'                   => ['nullable', 'string', 'max:30'],
            'phone_country_code'      => ['nullable', 'string', 'max:10'],
            'is_active'               => ['boolean'],
            'sort_order'              => ['integer', 'min:0'],
            'flag_icon'               => ['nullable', 'image', 'max:256'],
        ];
    }
}