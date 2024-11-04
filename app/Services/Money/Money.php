<?php

declare(strict_types=1);

namespace App\Services\Money;

use App\Enums\Currency;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

class Money extends AbstractMoney
{
    public const STORAGE_PRECISION = 6;

    public function __construct(int $amount, Currency $currency)
    {
        parent::__construct($amount, $currency);
    }

    public function discount(float $percentage): Money
    {
        $factor = 1 - ($percentage / 100);
    
        $factorInSmallestUnit = (int) round($factor * pow(10, self::STORAGE_PRECISION));
        $factorMoney = new Money($factorInSmallestUnit, $this->currency);

        return $this->multiply($factorMoney);
    }

    public function convert(Currency $toCurrency): Money
    {
        if ($this->currency === $toCurrency) {
            return $this;
        }

        $rates = Config::get('exchange_rates');
        if (!isset($rates['rates'][$this->currency->value]) || !isset($rates['rates'][$toCurrency->value])) {
            throw new InvalidArgumentException('Conversion rate not available for one or both currencies.');
        }

        $amountInBase = $this->amount / $rates['rates'][$this->currency->value];

        $convertedAmount = (int) round($amountInBase * $rates['rates'][$toCurrency->value]);

        return new Money($convertedAmount, $toCurrency);
    }

    /**
     *
     * @param int $storedAmount
     * @param Currency $currency
     * @return static
     */
    public static function fromStoredAmount(int $storedAmount, Currency $currency): static
    {
        return new static($storedAmount, $currency);
    }
}
