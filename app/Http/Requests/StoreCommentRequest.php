<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
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
            'content' => 'required|string|max:1000'
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
            'content.required' => '留言內容不得為空',
            'content.string' => '留言內容必須為文字格式',
            'content.max' => '留言內容最多1000個字元'
        ];
    }
}
