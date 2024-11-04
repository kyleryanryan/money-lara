<?php

namespace App\Rules;

use Closure;
use App\Enums\Currency;
use Illuminate\Contracts\Validation\ValidationRule;

class CurrencyPrecisionRule implements ValidationRule
{
    private int $precision;

    public function __construct(int $currencyValue)
    {
        $this->precision = Currency::from($currencyValue)->decimals();
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->precision === 0) {
            $pattern = '/^\d+$/';
        } else {
            $pattern = '/^\d+(\.\d{1,' . $this->precision . '})?$/';
        }

        if (!preg_match($pattern, (string) $value)) {
            $fail("The {$attribute} must have no more than {$this->precision} decimal places for the selected currency.");
        }
    }
}
