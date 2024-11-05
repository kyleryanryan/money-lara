<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplyPercentDiscountRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'required|uuid|exists:products,id',
            'discount_percentage' => 'required|numeric|min:1|max:100',
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

    public function getDiscountPercentage(): float
    {
        return $this->input('discount_percentage');
    }
}
