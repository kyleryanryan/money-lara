<?php

namespace App\Commands\Cart;

class AddToCartCommand
{
    public function __construct(
        private array $productIds
    ) {}

    public function getProductIds(): array
    {
        return $this->productIds;
    }
}
