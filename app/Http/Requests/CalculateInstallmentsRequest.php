<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalculateInstallmentsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'required|uuid|exists:products,id',
            'installments' => 'required|integer|min:1',
        ];
    }

    public function getProductIds(): array
    {
        return $this->input('product_ids');
    }

    public function getInstallments(): int
    {
        return $this->input('installments');
    }
}
