<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            //dữ liệu phải là một mảng; tối đa 3 đường dẫn ảnh.
            'delete_images' => ['sometimes', 'array', 'max:3'],
            //mỗi phần tử phải là chuỗi và không được trùng nhau.
            'delete_images.*' => ['required', 'string', 'distinct'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'gt:0', 'max:9999999999.99', 'decimal:0,2'],
            'category' => ['nullable', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'is_on_sale' => ['sometimes', 'boolean'],
            'discount_percent' => ['sometimes', 'integer', 'between:0,100'],
            'company_profile' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
