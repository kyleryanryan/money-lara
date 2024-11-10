<?php

namespace App\Http\Responses;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResponse extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'products' => $this->getProducts(),
            'total' => $this->getTotal(),
        ];
    }

    public function getProducts(): array
    {
        return $this->resource['products'];
    }

    public function getTotal(): string
    {
        $totalMoney = $this->resource['total'];
        return $totalMoney->formatAmountWithSymbol();
    }
}
