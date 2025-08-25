<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShortUrlRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 規則
     * - original_url: 必填且為 URL
     * - short_code: 可選、英數、長度 4 ~32、唯一
     * - expired_at: 可選、日期、必需晚於現在
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'original_url' => ['required', 'url', 'max:2048'],
            'short_code' => ['nullable', 'regex:/^[A-Za-z0-9]{4,32}$/', 'unique:short_urls,short_code'],
            'expired_at' => ['nullable', 'date', 'after:now'],
        ];
    }

    /**
     * 
     * @return array
     */
    public function messages(): array
    {
        return [
            'original_url.required' => '原始網址為必填',
            'original_url.url' => '原始網址必須為正確之 URL 格式',
            'short_code.regex' => '短碼僅能包含英數字，長度 4~32。',
            'short_code.unique' => '該短網址已被使用',
            'expired_at.after' => '過期時間必須比今日晚',
            'expired_at.date' => '過期時間必須為日期格式 xxxx-yy-zz 00:00:00'
        ];
    }
}
