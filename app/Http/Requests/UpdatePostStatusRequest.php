<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 設定驗證規則
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|integer|in:1,2,3', // 1:草稿, 2:發布, 3:隱藏
        ];
    }

    /**
     * 自訂錯誤訊息
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => '狀態為必填',
            'status.integer' => '狀態必須為整數',
            'status.in' => '狀態僅允許 1(草稿)、2(發布)、3(隱藏)',
        ];
    }
}
