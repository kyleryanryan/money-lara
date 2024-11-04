<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'required|uuid|exists:products,id',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function getProductIds(): array
    {
        return $this->input('product_ids');
    }
}
