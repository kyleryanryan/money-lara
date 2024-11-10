<?php

namespace App\Http\Responses;

use Illuminate\Http\Resources\Json\JsonResource;

class DiscountedCartResponse extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'products' => $this->getProducts(),
            'subTotal' => $this->getSubTotal(),
            'total' => $this->getTotal(),
            'discount' => $this->getDiscount(),
        ];
    }

    public function getProducts(): array
    {
        return $this->resource['products'];
    }

    public function getSubTotal(): string
    {
        $subTotalMoney = $this->resource['subTotal'];
        return $subTotalMoney->formatAmountWithSymbol();
    }

    public function getTotal(): string
    {
        $totalMoney = $this->resource['total'];
        return $totalMoney->formatAmountWithSymbol();
    }

    public function getDiscount(): string
    {
        $discountMoney = $this->resource['discount'];
        return $discountMoney;
    }
}
