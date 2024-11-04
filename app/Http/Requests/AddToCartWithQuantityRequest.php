<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartWithQuantityRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => 'required|uuid|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ];
    }

    public function getProductId(): string
    {
        return $this->input('product_id');
    }

    public function getQuantity(): int
    {
        return $this->input('quantity');
    }
}
