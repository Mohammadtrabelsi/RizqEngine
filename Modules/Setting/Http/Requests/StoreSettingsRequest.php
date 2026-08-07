<?php

namespace Modules\Setting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreSettingsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|max:255',
            'company_phone' => 'required|string|max:255',
            'notification_email' => 'required|email|max:255',
            'company_address' => 'required|string|max:500',
            'default_currency_id' => 'required|numeric',
            'default_currency_position' => 'required|string|max:255',
            'footer_text' => 'nullable|string|max:255',
            'default_product_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
            'default_category_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('access_settings');
    }
}
