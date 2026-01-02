<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetUserPostsRequest extends FormRequest
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
            'status' => 'nullable|integer|in:1,2,3', // 1:草稿, 2:發布, 3:隱藏
            'per_page' => 'nullable|integer|min:1|max:100', // 每頁筆數，最多 100
            'page' => 'nullable|integer|min:1', // 頁碼
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
            'status.integer' => '狀態必須為整數',
            'status.in' => '狀態僅允許 1(草稿)、2(發布)、3(隱藏)',
            'per_page.integer' => '每頁筆數必須為整數',
            'per_page.min' => '每頁筆數最少為 1',
            'per_page.max' => '每頁筆數最多為 100',
            'page.integer' => '頁碼必須為整數',
            'page.min' => '頁碼最少為 1',
        ];
    }
}
