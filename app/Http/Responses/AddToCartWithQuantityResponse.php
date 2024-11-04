<?php

namespace App\Http\Responses;

use Illuminate\Http\Resources\Json\JsonResource;

class AddToCartWithQuantityResponse extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'product' => $this->getProduct(),
            'unit_price' => $this->getUnitPrice(),
            'quantity' => $this->getQuantity(),
            'total_price' => $this->getTotalPrice(),
            'total_price_internal' => $this->getTotalPriceInternal(),
            'currency' => $this->getCurrency(),
        ];
    }

    public function getProduct(): array
    {
        return [
            'id' => $this->resource['product']->id,
            'name' => $this->resource['product']->name,
        ];
    }

    public function getUnitPrice(): string
    {
        return $this->resource['unit_price']->displayAmount();
    }

    public function getQuantity(): int
    {
        return $this->resource['quantity'];
    }

    public function getTotalPrice(): string
    {
        return $this->resource['total_price']->displayAmount();
    }

    public function getCurrency(): string
    {
        return $this->resource['unit_price']->getCurrency()->symbol();
    }

    public function getTotalPriceInternal(): int
    {
        return $this->resource['total_price']->getAmount();
    }
}
