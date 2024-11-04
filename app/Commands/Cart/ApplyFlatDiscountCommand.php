<?php

namespace App\Commands\Cart;

class ApplyFlatDiscountCommand
{
    public function __construct(
        private array $productIds,
        private float $discount
    ) {}

    public function getProductIds(): array
    {
        return $this->productIds;
    }

    public function getDiscount(): float
    {
        return $this->discount;
    }
}
