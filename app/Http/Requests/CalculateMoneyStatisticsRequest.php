<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Product;
use App\Services\Money\Money;

class CalculateMoneyStatisticsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'required|uuid|exists:products,id',
        ];
    }

    public function getValidatedMoneyValues(): array
    {
        $products = Product::whereIn('id', $this->input('product_ids'))->get();

        $currency = null;
        $moneyValues = [];

        /** @var Product $product */
        foreach ($products as $product) {
            /** @var Money $money */
            $money = $product->getMoney();

            if (is_null($currency)) {
                $currency = $money->getCurrency();
            } elseif ($money->getCurrency() !== $currency) {
                throw new \InvalidArgumentException('All products must have the same currency.');
            }

            $moneyValues[] = $money;
        }

        return $moneyValues;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed',
            ], 422)
        );
    }
}
