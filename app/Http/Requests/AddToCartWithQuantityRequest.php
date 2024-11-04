<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartWithQuantityRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => 'required|uuid|exists:products,id',
            'quantity' => 'required|min:1',
        ];
    }

    public function getProductId(): string
    {
        return $this->input('product_id');
    }

    //temporarily changed to float for experimental purposes
    public function getQuantity(): float
    {
        return $this->input('quantity');
    }
}
