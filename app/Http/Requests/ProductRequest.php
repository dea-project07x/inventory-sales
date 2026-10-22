<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-master') ?? false;
    }

    public function rules(): array
    {
        $rules = [
            'sku' => [
                'required', 'string', 'max:50',
                Rule::unique('products', 'sku')->ignore($this->route('product')),
            ],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'purchase_price' => ['required', 'numeric', 'min:0', 'max:9999999999999'],
            'selling_price' => ['required', 'numeric', 'min:0', 'max:9999999999999'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
        ];

        // Stok awal hanya saat membuat produk; setelah itu stok berubah lewat transaksi.
        if ($this->isMethod('post')) {
            $rules['stock'] = ['required', 'integer', 'min:0'];
        }

        return $rules;
    }
}
