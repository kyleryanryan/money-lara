<?php

namespace App\Http\Requests;

use App\Enums\Currency;
use App\Rules\CurrencyPrecisionRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateProductRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }

    public function rules(): array
    {
        $currency = $this->input('currency');
 
        return [
            'name' => 'required|string|max:255',
            'price' => ['required','numeric', new CurrencyPrecisionRule($currency)],
            'currency' => ['required', new Enum(Currency::class)],
        ];
    }

    public function getName(): string
    {
        return $this->input('name');
    }

    public function getPrice(): float
    {
        return $this->input('price');
    }

    public function getCurrency(): Currency
    {
        return Currency::from($this->input('currency'));
    }
}
