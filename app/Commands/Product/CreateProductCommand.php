<?php

namespace App\Commands\Product;

use App\Enums\Currency;

class CreateProductCommand
{
    public function __construct(
        private string $name,
        private float $price,
        private Currency $currency
    ){
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }
}
