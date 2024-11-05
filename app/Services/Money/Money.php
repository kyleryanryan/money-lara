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
    public const REMAINDER_PRECISION = 4;
    private string $remainder = '0';

    public function __construct(
        private int $amount,
        private Currency $currency
    ){
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
        $other = (string)"{$other->getAmount()}.{$other->getRemainder()}";
        $self = (string)"{$this->getAmount()}.{$this->getRemainder()}";

        $sum = bcadd($other, $self, self::REMAINDER_PRECISION);

        [$integerPart, $fractionalPart] = explode('.', $sum);

        $integerPart = (int) $integerPart;
        $fractionalPart = rtrim($fractionalPart, '0');

        $result = new static($integerPart, $this->currency);
        $result->setRemainder($fractionalPart);
        return $result;
    }

    public function subtract(Money $other): static
    {
        $this->assertSameCurrency($other);
        $other = (string)"{$other->getAmount()}.{$other->getRemainder()}";
        $self = (string)"{$this->getAmount()}.{$this->getRemainder()}";

        $sub = bcsub($self, $other, self::REMAINDER_PRECISION + self::STORAGE_PRECISION);

        [$integerPart, $fractionalPart] = explode('.', $sub);

        $integerPart = (int) $integerPart;
        $fractionalPart = rtrim($fractionalPart, '0');

        $result = new static($integerPart, $this->currency);
        $result->setRemainder($fractionalPart);
        return $result;
    }

    public function multiply(Money $factor): self
    {
        $this->assertSameCurrency($factor);

        $multiplier = "{$factor->getAmount()}.{$factor->getRemainder()}";
        $multiplicand = "{$this->getAmount()}.{$this->remainder}";

        $multiplierFormat = bcdiv($multiplier, (string)pow(10, self::STORAGE_PRECISION), self::STORAGE_PRECISION + self::REMAINDER_PRECISION);
        $multiplicandFormat = bcdiv($multiplicand, (string)pow(10, self::STORAGE_PRECISION), self::STORAGE_PRECISION + self::REMAINDER_PRECISION);

        $rawResult = bcmul($multiplierFormat, $multiplicandFormat, self::STORAGE_PRECISION + self::REMAINDER_PRECISION);
        $scaledResult = bcmul($rawResult, (string)pow(10, self::STORAGE_PRECISION), self::STORAGE_PRECISION + self::REMAINDER_PRECISION);

        [$integerPart, $fractionalPart] = explode('.', $scaledResult . '.0');

        $result = new self((int)$integerPart, $this->currency);
        $result->setRemainder(rtrim($fractionalPart, '0'));

        return $result;
    }

    public function divide(Money $divisor): self
    {
        $this->assertSameCurrency($divisor);
    
        if ($divisor->getAmount() === 0) {
            throw new InvalidArgumentException('Division by zero is not allowed.');
        }

        $dividend = "{$this->getAmount()}.{$this->remainder}";
        $divisorValue = "{$divisor->getAmount()}.{$divisor->getRemainder()}";

        $dividendFormat = bcdiv($dividend, (string)pow(10, self::STORAGE_PRECISION), self::STORAGE_PRECISION + self::REMAINDER_PRECISION);
        $divisorFormat = bcdiv($divisorValue, (string)pow(10, self::STORAGE_PRECISION), self::STORAGE_PRECISION + self::REMAINDER_PRECISION);

        $rawResult = bcdiv($dividendFormat, $divisorFormat, self::STORAGE_PRECISION + self::REMAINDER_PRECISION);
        $scaledResult = bcmul($rawResult, (string)pow(10, self::STORAGE_PRECISION), self::STORAGE_PRECISION + self::REMAINDER_PRECISION);

        [$integerPart, $fractionalPart] = explode('.', $scaledResult . '.0');

        $result = new self((int)$integerPart, $this->currency);
        $result->setRemainder(rtrim($fractionalPart, '0'));
    
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

        $amountInBase = bcdiv((string)$this->getAmount(), (string)$rates['rates'][$this->currency->value], self::STORAGE_PRECISION + self::REMAINDER_PRECISION);
        $convertedAmount = bcmul($amountInBase, (string)$rates['rates'][$toCurrency->value], self::STORAGE_PRECISION + self::REMAINDER_PRECISION);

        [$integerPart, $fractionalPart] = explode('.', $convertedAmount . '.0');

        $result = new static((int)$integerPart, $toCurrency);
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
        return new static($storedAmount, $currency);
    }

    protected function assertSameCurrency(Money $other): void
    {
        if ($this->currency !== $other->getCurrency()) {
            throw new InvalidArgumentException('Currencies must match for arithmetic operations.');
        }
    }
}
