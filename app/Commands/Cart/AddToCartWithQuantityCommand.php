<?php

namespace App\Commands\Cart;

class AddToCartWithQuantityCommand
{
    public function __construct(
        private string $productId,
        private float $quantity
    ) {}

    public function getProductId(): string
    {
        return $this->productId;
    }

    //temporarily changed to float for experimental purposes
    public function getQuantity(): float
    {
        return $this->quantity;
    }
}
