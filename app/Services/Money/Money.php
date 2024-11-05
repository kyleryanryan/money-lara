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
    public const REMAINDER_PRECISION = 2;
    private string $remainder = '0';

    public function __construct(
        private int $amount,
        private Currency $currency
    ){
    }

    public static function fromFloat(float $value, Currency $currency): static
    {
        $amount = NumberHelper::floatToInt($value, self::STORAGE_PRECISION);
        return new static(amount: $amount, currency: $currency);
    }

    public static function fromInt(int $value, Currency $currency): static
    {
        return new static(amount: $value, currency: $currency);
    }

    public function getRemainder(): string
    {
        return $this->remainder;
    }

    public function setRemainder(string $remainder): void
    {
        $this->remainder = $remainder;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function displayAmount(): string
    {
        $floatAmount = NumberHelper::intToFloat($this->getAmount(), self::STORAGE_PRECISION, $this->currency->decimals());
        return number_format($floatAmount, $this->currency->decimals());
    }

    public function add(Money $other): static
    {
        $this->assertSameCurrency($other);

        $other = $this->formatAmountWithRemainder($other->getAmount(), $other->getRemainder());
        $self = $this->formatAmountWithRemainder($this->getAmount(), $this->getRemainder());

        $sum = bcadd($other, $self, self::REMAINDER_PRECISION);

        [$integerPart, $fractionalPart] = explode('.', $sum);

        $integerPart = (int) $integerPart;
        $fractionalPart = rtrim($fractionalPart, '0');

        $result = self::fromInt($integerPart, $this->currency);
        $result->setRemainder($fractionalPart);
        return $result;
    }

    public function subtract(Money $other): static
    {
        $this->assertSameCurrency($other);

        $other = $this->formatAmountWithRemainder($other->getAmount(), $other->getRemainder());
        $self = $this->formatAmountWithRemainder($this->getAmount(), $this->getRemainder());

        $sub = bcsub($self, $other, self::REMAINDER_PRECISION + self::STORAGE_PRECISION);

        [$integerPart, $fractionalPart] = explode('.', $sub);

        $integerPart = (int) $integerPart;
        $fractionalPart = rtrim($fractionalPart, '0');

        $result = self::fromInt($integerPart, $this->currency);
        $result->setRemainder($fractionalPart);
        return $result;
    }

    public function multiply(Money $factor): static
    {
        $this->assertSameCurrency($factor);

        $multiplier = $this->formatAmountWithRemainder($factor->getAmount(), $factor->getRemainder());
        $multiplicand = $this->formatAmountWithRemainder($this->getAmount(), $this->getRemainder());

        $rawResult = bcmul($multiplier, $multiplicand, self::REMAINDER_PRECISION);
        $amountResult = bcdiv($rawResult, (string)pow(10, self::STORAGE_PRECISION), self::STORAGE_PRECISION);

        [$integerPart, $fractionalPart] = explode('.', $amountResult . '.0');

        $result = self::fromInt((int)$integerPart, $this->currency);
        $result->setRemainder(rtrim($fractionalPart, '0'));

        return $result;
    }

    public function divide(Money $divisor): static
    {
        $this->assertSameCurrency($divisor);
        
        if ($divisor->getAmount() === 0) {
            throw new InvalidArgumentException('Division by zero is not allowed.');
        }

        $dividend = $this->formatAmountWithRemainder($this->getAmount(), $this->getRemainder());
        $divisorValue = $this->formatAmountWithRemainder($divisor->getAmount(), $divisor->getRemainder());
    
        $rawResult = bcdiv($dividend, $divisorValue, self::STORAGE_PRECISION + self::REMAINDER_PRECISION);
        $scaledResult = bcmul($rawResult, (string)pow(10, self::STORAGE_PRECISION), self::STORAGE_PRECISION + self::REMAINDER_PRECISION);

        [$integerPart, $fractionalPart] = explode('.', $scaledResult . '.0');

        $result = self::fromInt((int)$integerPart, $this->currency);
        $result->setRemainder(rtrim($fractionalPart, '0'));

        return $result;
    }

    public function discount(float $percentage): static
    {
        $factor = 1 - ($percentage / 100);

        $factorMoney = Money::fromFloat($factor, $this->currency);

        return $this->multiply($factorMoney);
    }

    public function convert(Currency $toCurrency): static
    {
        if ($this->currency === $toCurrency) {
            return $this;
        }

        $rates = Config::get('exchange_rates');
        if (!isset($rates['rates'][$this->currency->value]) || !isset($rates['rates'][$toCurrency->value])) {
            throw new InvalidArgumentException('Conversion rate not available for one or both currencies.');
        }

        $amountInBase = bcdiv((string)$this->getAmount(), (string)$rates['rates'][$this->currency->value], self::STORAGE_PRECISION + self::REMAINDER_PRECISION);
        $convertedAmount = bcmul($amountInBase, (string)$rates['rates'][$toCurrency->value], self::STORAGE_PRECISION + self::REMAINDER_PRECISION);

        [$integerPart, $fractionalPart] = explode('.', $convertedAmount . '.0');

        $result = self::fromInt((int)$integerPart, $toCurrency);
        $result->setRemainder(rtrim($fractionalPart, '0'));

        return $result;
    }

    /**
     *
     * @param int $storedAmount
     * @param Currency $currency
     * @return static
     */
    public static function fromStoredAmount(int $storedAmount, Currency $currency): static
    {
        return self::fromInt($storedAmount, $currency);
    }

    protected function assertSameCurrency(Money $other): void
    {
        if ($this->currency !== $other->getCurrency()) {
            throw new InvalidArgumentException('Currencies must match for arithmetic operations.');
        }
    }

    /**
     * Concatenates the amount and remainder as a formatted string.
     */
    private function formatAmountWithRemainder(int $amount, string $remainder): string
    {
        return "{$amount}.{$remainder}";
    }
}
