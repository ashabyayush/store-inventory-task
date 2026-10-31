<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:products,code'],
            'price' => ['required', 'numeric', 'min:0'],
            'tax_percent' => ['required', 'numeric', 'between:0,100'],
            'stock' => ['required', 'integer', 'min:0'],
        ];
    }
}