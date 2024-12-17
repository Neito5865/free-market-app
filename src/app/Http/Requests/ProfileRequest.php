<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'image' => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'name' => 'required|string|max:40',
            'post_code' => 'nullable|regex:/^\d{3}-\d{4}$/',
            'address' => 'nullable|string',
            'building' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'image.mimes' => '画像は拡張子がjpg/jpeg/pngのいずれかである必要があります',
            'image.max' => '2MB以下の画像を選択してください',
            'name.required' => 'お名前を入力してください',
            'name.string' => 'お名前は文字列で入力してください',
            'name.max' => 'お名前は40文字以内で入力してください',
            'post_code.regex' => '郵便番号は「123-4567」の形式で入力してください',
            'address.string' => '住所は文字列で入力してください',
            'building.string' => '建物名は文字列で入力してください',
        ];
    }
}
