<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Chưa tích hợp auth đầy đủ, cho phép mọi người gửi review
        return true;
    }

    public function rules(): array
    {
        return [
            'author'  => 'required|string|max:255',
            'content' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'author.required'  => 'Vui lòng nhập tên.',
            'author.max'       => 'Tên tối đa 255 ký tự.',
            'content.required' => 'Vui lòng nhập nội dung đánh giá.',
            'content.max'      => 'Nội dung tối đa 1000 ký tự.',
        ];
    }
}
