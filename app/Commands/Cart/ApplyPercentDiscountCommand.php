<?php

namespace App\Commands\Cart;

class ApplyPercentDiscountCommand
{
    public function __construct(
        private array $productIds,
        private float $discountPercentage
    ) {}

    public function getProductIds(): array
    {
        return $this->productIds;
    }

    public function getDiscountPercentage(): float
    {
        return $this->discountPercentage;
    }
}
