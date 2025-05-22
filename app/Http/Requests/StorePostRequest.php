<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:1,2' // 1 草稿, 2 發布
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
            'title.required' => '標題會必填',
            'title.max' => '標題最多255個字元',
            'content.required' => '文章內容為必填',
            'status.required' => '狀態為必填',
            'status.in' => '狀態僅允許 1(草稿)、2(發布)'
        ];
    }
}
