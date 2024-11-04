<?php

namespace App\Commands\Product;

use App\Enums\Currency;

class CreateProductCommand
{
    public function __construct(
        private string $name,
        private int $priceInSmallestUnit,
        private Currency $currency
    ){
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPriceInSmallestUnit(): int
    {
        return $this->priceInSmallestUnit;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }
}
