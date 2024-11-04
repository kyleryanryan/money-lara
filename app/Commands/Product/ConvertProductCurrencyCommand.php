<?php

namespace App\Commands\Product;

use App\Enums\Currency;

class ConvertProductCurrencyCommand
{
    public function __construct(
        private string $productId,
        private Currency $targetCurrency
    ) {}

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getTargetCurrency(): Currency
    {
        return $this->targetCurrency;
    }
}
