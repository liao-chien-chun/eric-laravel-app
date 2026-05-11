<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchPostsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // 前台公開 API，不需要驗證
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'keyword' => 'sometimes|string|max:255',
            'sort_by' => 'sometimes|string|in:created_at,views_count,comments_count',
            'order' => 'sometimes|string|in:asc,desc',
            'per_page' => 'sometimes|integer|min:1|max:50',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'keyword.string' => '搜尋關鍵字必須是字串',
            'keyword.max' => '搜尋關鍵字不能超過 255 個字元',
            'sort_by.in' => '排序欄位必須是 created_at、views_count 或 comments_count',
            'order.in' => '排序方向必須是 asc 或 desc',
            'per_page.integer' => '每頁筆數必須是整數',
            'per_page.min' => '每頁筆數最少為 1',
            'per_page.max' => '每頁筆數最多為 50',
        ];
    }

    /**
     * Get sanitized search parameters with defaults.
     */
    public function getSearchParams(): array
    {
        return [
            'keyword' => $this->input('keyword'),
            'sort_by' => $this->input('sort_by', 'created_at'),
            'order' => $this->input('order', 'desc'),
            'per_page' => $this->input('per_page', 15),
        ];
    }
}
