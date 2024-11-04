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
    private const REMAINDER_PRECISION = 6;
    private int $remainder = 0;

    public function __construct(
        private int $amount,
        private Currency $currency
    ){
    }

    public function getRemainder(): int
    {
        return $this->remainder;
    }

    public function setRemainder(int $remainder): void
    {
        $this->remainder = $remainder;
    }

    public function multiply(Money $factor): self
    {
        $this->assertSameCurrency($factor);

        // Convert both amounts to the decimal units and add remainders
        $multiplier = ((float)"{$factor->getAmount()}.{$factor->getRemainder()}") / pow(10, self::STORAGE_PRECISION);;
        $multiplicand = ((float)"{$this->amount}.{$this->remainder}") / pow(10, self::STORAGE_PRECISION);
        $rawResult = $multiplier * $multiplicand;

        $result = $rawResult * pow(10, self::STORAGE_PRECISION);
        $integerPart = (int) $result;
        $fractionalPart = (int) $result - $integerPart;
        $result = new self($integerPart, $this->currency);

        $result->setRemainder((int) $fractionalPart);

        return $result;
    }

    public function divide(Money $divisor): self
    {
        $this->assertSameCurrency($divisor);
    
        if ($divisor->getAmount() === 0) {
            throw new InvalidArgumentException('Division by zero is not allowed.');
        }

        $scaledAmount = $this->amount * pow(10, self::STORAGE_PRECISION) + $this->remainder;
        $divisorAmount = $divisor->getAmount() * pow(10, self::STORAGE_PRECISION) + $divisor->getRemainder();
    
        $rawResult = $scaledAmount / $divisorAmount;
        $scaledResult = $rawResult * pow(10, self::STORAGE_PRECISION);
    
        $integerPart = (int) $scaledResult;
        $fractionalPart = (int) $scaledResult - $integerPart;
    
        $result = new self($integerPart, $this->currency);
        $result->setRemainder($fractionalPart);

        return $result;
    }

    public function discount(float $percentage): self
    {
        $factor = 1 - ($percentage / 100);
    
        $factorInSmallestUnit = (int) ($factor * pow(10, self::STORAGE_PRECISION));
        $factorMoney = new Money($factorInSmallestUnit, $this->currency);

        return $this->multiply($factorMoney);
    }

    public function convert(Currency $toCurrency): self
    {
        if ($this->currency === $toCurrency) {
            return $this;
        }

        $rates = Config::get('exchange_rates');
        if (!isset($rates['rates'][$this->currency->value]) || !isset($rates['rates'][$toCurrency->value])) {
            throw new InvalidArgumentException('Conversion rate not available for one or both currencies.');
        }

        $amountInBase = $this->amount / $rates['rates'][$this->currency->value];

        $convertedAmount = $amountInBase * $rates['rates'][$toCurrency->value];
        $integerPart = (int) $convertedAmount;
        $fractionalPart = (int) $convertedAmount - $integerPart;
   
        $result = new static($integerPart, $toCurrency);
        $result->setRemainder($fractionalPart);

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
        $other = (float)"{$other->getAmount()}.{$other->getRemainder()}";
        $self = (float)"{$this->amount}.{$this->remainder}";

        $sum = $other + $self;
        $integerPart = (int) $sum;
        $fractionalPart = (int) $sum - $integerPart;

        $result = new static($integerPart, $this->currency);
        $result->setRemainder($fractionalPart);
        return $result;
    }

    public function subtract(Money $other): static
    {
        $this->assertSameCurrency($other);
        $other = (float)"{$other->getAmount()}.{$other->getRemainder()}";
        $self = (float)"{$this->amount}.{$this->remainder}";

        $sub = $other - $self;
        $integerPart = (int) $sub;
        $fractionalPart = (int) $sub - $integerPart;

        $result = new static($integerPart, $this->currency);
        $result->setRemainder($fractionalPart);
        return $result;
    }

    protected function assertSameCurrency(Money $other): void
    {
        if ($this->currency !== $other->getCurrency()) {
            throw new InvalidArgumentException('Currencies must match for arithmetic operations.');
        }
    }
}
