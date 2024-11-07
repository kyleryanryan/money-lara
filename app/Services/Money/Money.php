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
    private const REMAINDER_PRECISION = 2;
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
    public static function fromScaledInt(int $value, Currency $currency): static
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
        $sum = bcadd($this->getFormattedAmountWithRemainder(), $other->getFormattedAmountWithRemainder(), self::REMAINDER_PRECISION);
        return $this->setFromResult($sum);
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
        $difference = bcsub($this->getFormattedAmountWithRemainder(), $other->getFormattedAmountWithRemainder(), self::STORAGE_PRECISION + self::REMAINDER_PRECISION);
        return $this->setFromResult($difference);
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

        $product = bcmul($this->getFormattedAmountWithRemainder(), $factor->getFormattedAmountWithRemainder(), self::REMAINDER_PRECISION);
        $scaledProduct = bcdiv($product, (string)pow(10, self::STORAGE_PRECISION), self::STORAGE_PRECISION);
        
        return $this->setFromResult($scaledProduct);
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

        $quotient = bcdiv($this->getFormattedAmountWithRemainder(), $divisor->getFormattedAmountWithRemainder(), self::STORAGE_PRECISION + self::REMAINDER_PRECISION);
        $scaledQuotient = bcmul($quotient, (string)pow(10, self::STORAGE_PRECISION), self::STORAGE_PRECISION + self::REMAINDER_PRECISION);
        
        return $this->setFromResult($scaledQuotient);
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

        $baseAmount = bcdiv(
            $this->getFormattedAmountWithRemainder(), 
            (string)$rates['rates'][$this->currency->value], 
            self::STORAGE_PRECISION + self::REMAINDER_PRECISION
        );
        $convertedAmount = bcmul($baseAmount, (string)$rates['rates'][$toCurrency->value], self::STORAGE_PRECISION + self::REMAINDER_PRECISION);
    
        return $this->setFromResult($convertedAmount, $toCurrency);
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
        return array_reduce($moneyArray, fn($carry, $money) => $carry->add($money), self::fromScaledInt(0, $currency));
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

        return self::fromScaledInt($minAmount, $currency);
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

        return self::fromScaledInt($maxAmount, $currency);
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
     * Check if money objects have same currency
     * 
     * @return void
     */
    private function assertSameCurrency(Money $other): void
    {
        if ($this->currency !== $other->getCurrency()) {
            throw new InvalidArgumentException('Currencies must match for arithmetic operations.');
        }
    }

    /**
     * Assert that all Money objects in the array have the same currency.
     *
     * @param Money[] $moneyArray
     * @return void
     */
    private static function assertSameCurrencyArray(array $moneyArray): void
    {
        /** @var Money $money */
        foreach ($moneyArray as $money) {
            $money->assertSameCurrency($money);
        }
    }

    private function setFromResult(string $result, ?Currency $currency = null): static
    {
        [$integerPart, $fractionalPart] = explode('.', $result . '.0');
        $newMoney = self::fromScaledInt((int)$integerPart, $currency ?? $this->currency);
        $newMoney->setRemainder(rtrim($fractionalPart, '0'));
        return $newMoney;
    }

    private function getFormattedAmountWithRemainder(): string
    {
        return "{$this->amount}.{$this->remainder}";
    }
}
