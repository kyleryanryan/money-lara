<?php

namespace App\Commands\Cart;

use App\Enums\Currency;

class CalculateInstallmentsCommand
{
    public function __construct(
        private readonly array $productIds,
        private readonly int $installments,
    ) {}

    public function getProductIds(): array
    {
        return $this->productIds;
    }

    public function getInstallments(): int
    {
        return $this->installments;
    }
}
