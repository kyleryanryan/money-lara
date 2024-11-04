<?php

namespace App\Http\Requests;

use App\Enums\Currency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ConvertCurrencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'currency' => ['required', new Enum(Currency::class)],
        ];
    }

    public function getTargetCurrency(): Currency
    {
        return Currency::from($this->validated('currency'));
    }
}
