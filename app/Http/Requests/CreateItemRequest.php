<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateItemRequest extends FormRequest
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
            'name' => 'required|string|max:100',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:1,2' // 1 草稿, 2 上架
        ];
    }

    /**
     * 自訂錯誤訊息
     */
    public function messages(): array
    {
        return [
            'name.required' => '商品名稱必填',
            'name.max' => '商品名稱最多100個字元',
            'price.required' => '商品價格必填',
            'price.integer' => '商品價格只能為數字',
            'price.min' => '商品價格最低為0',
            'stock.required' => '庫存量為必填',
            'stock.integer' => '庫存量只能為數字',
            'stock.min' => '庫存最少必須為0',
            'status.required' => '商品狀態必填',
            'status.in' => '狀態僅允許 1(草稿)、2(發布)'
        ]; 
    } 
}
