<?php

namespace App\Http\Responses;

use Illuminate\Http\Resources\Json\JsonResource;

class AddToCartWithQuantityResponse extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'product' => $this->getProduct(),
            'unitPrice' => $this->getUnitPrice(),
            'quantity' => $this->getQuantity(),
            'totalPrice' => $this->getTotalPrice(),
            'totalPricePnternal' => $this->getTotalPriceInternal(),
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
        return $this->resource['unitPrice']->formatAmountWithSymbol();
    }

    public function getQuantity(): int
    {
        return $this->resource['quantity'];
    }

    public function getTotalPrice(): string
    {
        return $this->resource['totalPrice']->formatAmountWithSymbol();
    }

    public function getCurrency(): string
    {
        return $this->resource['unitPrice']->getCurrency()->symbol();
    }

    public function getTotalPriceInternal(): int
    {
        return $this->resource['totalPrice']->getAmount();
    }
}
