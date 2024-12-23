<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payment_method' => 'required|in:1,2',
            'selected_address' => ['required', 'array', function ($attribute, $value, $fail) {
                $requiredFields = ['name', 'post_code', 'address', 'building'];
                foreach ($requiredFields as $field) {
                    if (empty($value[$field])) {
                        return $fail('配送先を指定してください');
                    }
                }
            }],
        ];
    }

    public function messages()
    {
        return [
            'payment_method.required' => '支払い方法を選択してください',
            'payment_method.in' => '支払い方法が無効です',
            'selected_address.required' => '配送先を指定してください',
        ];
    }
}
