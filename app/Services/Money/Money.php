<?php

declare(strict_types=1);

namespace App\Services\Money;

use App\Enums\Currency;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;
use App\Support\NumberHelper;

class Money
{
    public const STORAGE_PRECISION = 6;

    public function __construct(
        private int $amount,
        private Currency $currency
    ){
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

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function displayAmount(): string
    {
        $floatAmount = NumberHelper::intToFloat($this->amount, self::STORAGE_PRECISION);
        return number_format($floatAmount, $this->currency->decimals());
    }

    public function add(Money $other): static
    {
        $this->assertSameCurrency($other);
        return new static($this->amount + $other->getAmount(), $this->currency);
    }

    public function subtract(Money $other): static
    {
        $this->assertSameCurrency($other);
        return new static($this->amount - $other->getAmount(), $this->currency);
    }

    public function multiply(Money $factor): static
    {
        $this->assertSameCurrency($factor);

        $multipliedAmount = (int) round($this->amount * ($factor->getAmount() / pow(10, self::STORAGE_PRECISION)));
        
        return new static($multipliedAmount, $this->currency);
    }
    
    public function divide(Money $divisor): static
    {
        $this->assertSameCurrency($divisor);
    
        if ($divisor->getAmount() === 0) {
            throw new InvalidArgumentException('Division by zero is not allowed.');
        }

        $dividedAmount = (int) round($this->amount / ($divisor->getAmount() / pow(10, self::STORAGE_PRECISION)));
        
        return new static($dividedAmount, $this->currency);
    }

    protected function assertSameCurrency(Money $other): void
    {
        if ($this->currency !== $other->getCurrency()) {
            throw new InvalidArgumentException('Currencies must match for arithmetic operations.');
        }
    }
}
