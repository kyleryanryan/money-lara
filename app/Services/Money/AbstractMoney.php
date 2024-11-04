<?php

declare(strict_types=1);

namespace App\Services\Money;

use App\Enums\Currency;
use App\Support\NumberHelper;
use InvalidArgumentException;

abstract class AbstractMoney
{
    public const STORAGE_PRECISION = 2;

    public function __construct(
        protected int $amount,
        protected Currency $currency
    ){
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

    public function add(AbstractMoney $other): static
    {
        $this->assertSameCurrency($other);
        return new static($this->amount + $other->getAmount(), $this->currency);
    }

    public function subtract(AbstractMoney $other): static
    {
        $this->assertSameCurrency($other);
        return new static($this->amount - $other->getAmount(), $this->currency);
    }

    public function multiply(AbstractMoney $factor): static
    {
        $this->assertSameCurrency($factor);

        $multipliedAmount = (int) round($this->amount * ($factor->getAmount() / pow(10, self::STORAGE_PRECISION)));
        
        return new static($multipliedAmount, $this->currency);
    }
    
    public function divide(AbstractMoney $divisor): static
    {
        $this->assertSameCurrency($divisor);
    
        if ($divisor->getAmount() === 0) {
            throw new InvalidArgumentException('Division by zero is not allowed.');
        }

        $dividedAmount = (int) round($this->amount / ($divisor->getAmount() / pow(10, self::STORAGE_PRECISION)));
        
        return new static($dividedAmount, $this->currency);
    }

    protected function assertSameCurrency(AbstractMoney $other): void
    {
        if ($this->currency !== $other->getCurrency()) {
            throw new InvalidArgumentException('Currencies must match for arithmetic operations.');
        }
    }
}
