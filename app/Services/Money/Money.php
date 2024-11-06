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

    /**
     * Convert float or unscaled integer to Money.
     * 
     * @param float $value
     * @param Currency $currency
     * @return static
     */
    public static function fromFloat(float $value, Currency $currency): static
    {
        $amount = NumberHelper::floatToInt($value, self::STORAGE_PRECISION);
        return new static(amount: $amount, currency: $currency);
    }

    /**
     * Convert integer to Money.(already scaled to storage precision)
     * 
     * @param int $value
     * @param Currency $currency
     * @return static
     */
    public static function fromInt(int $value, Currency $currency): static
    {
        return new static(amount: $value, currency: $currency);
    }

    /**
     * Return the remainder from calculations in money object to the other
     * 
     * @return string
     */
    public function getRemainder(): string
    {
        return $this->remainder;
    }

    /**
     * Set the remainder from calculations in money object to the other
     * Can only be set after calculations and cannot be initialized
     * 
     * @param string $remainder
     */
    private function setRemainder(string $remainder): void
    {
        $this->remainder = $remainder;
    }

    /**
     * Get the currency of the money object
     * 
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * Get the amount of the money object
     * 
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * Display the amount in correct format(final amount) within currency decimal places
     * 
     * @return string
     */
    public function displayAmount(): string
    {
        $floatAmount = NumberHelper::intToFloat($this->getAmount(), self::STORAGE_PRECISION, $this->currency->decimals());
        return number_format($floatAmount, $this->currency->decimals());
    }

    /**
     * Add two money objects
     * 
     * @param Money $other
     * @return static
     */
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

    /**
     * Subtract two money objects
     * 
     * @param Money $other
     * @return static
     */
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

    /**
     * Multiply two money objects
     * 
     * @param Money $factor
     * @return static
     */
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

    /**
     * Divide two money objects
     * 
     * @param Money $divisor
     * @return static
     */
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

    /**
     * Calculate the percentage of the money object
     * 
     * @param float $percentage
     * @return static
     */
    public function discount(float $percentage): static
    {
        $factor = 1 - ($percentage / 100);

        $factorMoney = Money::fromFloat($factor, $this->currency);

        return $this->multiply($factorMoney);
    }

    /**
     * Convert the money object to another currency
     * 
     * @param Currency $toCurrency
     * @return static
     */
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

    /**
     * Check if money objects have same currency
     * 
     * @return void
     */
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

    /**
     * Calculate the total of an array of Money objects.
     *
     * @param Money[] $moneyArray
     * @return Money
     */
    public static function total(array $moneyArray): Money
    {
        self::assertSameCurrencyArray($moneyArray);

        $currency = $moneyArray[0]->getCurrency();
        return array_reduce($moneyArray, fn($carry, $money) => $carry->add($money), self::fromInt(0, $currency));
    }

    /**
     * Get the lowest value in an array of Money objects.
     *
     * @param Money[] $moneyArray
     * @return Money
     */
    public static function lowest(array $moneyArray): Money
    {
        self::assertSameCurrencyArray($moneyArray);

        $currency = $moneyArray[0]->getCurrency();
        $minAmount = min(array_map(fn($money) => $money->getAmount(), $moneyArray));

        return self::fromInt($minAmount, $currency);
    }

    /**
     * Get the highest value in an array of Money objects.
     *
     * @param Money[] $moneyArray
     * @return Money
     */
    public static function highest(array $moneyArray): Money
    {
        self::assertSameCurrencyArray($moneyArray);

        $currency = $moneyArray[0]->getCurrency();
        $maxAmount = max(array_map(fn($money) => $money->getAmount(), $moneyArray));

        return self::fromInt($maxAmount, $currency);
    }

    /**
     * Calculate the average of an array of Money objects.
     *
     * @param Money[] $moneyArray
     * @return Money
     */
    public static function average(array $moneyArray): Money
    {
        self::assertSameCurrencyArray($moneyArray);

        $currency = $moneyArray[0]->getCurrency();
        $total = self::total($moneyArray);
        $count = count($moneyArray);

        return $total->divide(self::fromFloat($count, $currency));
    }

    /**
     * Assert that all Money objects in the array have the same currency.
     *
     * @param Money[] $moneyArray
     * @return void
     */
    private static function assertSameCurrencyArray(array $moneyArray): void
    {
        $currency = $moneyArray[0]->getCurrency();

        foreach ($moneyArray as $money) {
            if ($money->getCurrency() !== $currency) {
                throw new InvalidArgumentException('All Money objects must have the same currency.');
            }
        }
    }
}
