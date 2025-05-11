<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
        ];
    }

    /**
     * 自訂錯誤訊息
     * @return array<string, string>
     */
    public function messages(): array 
    {
        return [
            'name.required' => '姓名為必填欄位',
            'name.max' => '姓名長度不得超過50字',
            'email.required' => '電子郵件為必填欄位',
            'email.email' => '電子郵件格式不正確',
            'email.unique' => '該 email 已經被註冊過了',
            'password.required' => '密碼為必填欄位',
            'password.min' => '密碼長度至少6個字',
            'password.confirmed' => '密碼確認與密碼輸入不一致',
            'phone.integer' => '電話號碼格式不正確',
        ];
    }
}
