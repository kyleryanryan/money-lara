<?php

namespace App\Http\Responses\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ConvertProductCurrencyResponse extends JsonResource
{
    public function toArray($request)
    {
        return [
            'original_price' => $this->getOriginalPrice(),
            'original_price_internal' => $this->getOriginalPriceInternal(),
            'converted_price' => $this->getConvertedPrice(),
            'converted_price_internal' => $this->getConvertedPriceInternal(),
            'currency' => $this->getCurrencySymbol(),
        ];
    }

    public function getOriginalPrice(): string
    {
        return $this->resource['original_price']->displayAmount() . ' ' . $this->resource['original_price']->getCurrency()->symbol();
    }

    public function getOriginalPriceInternal(): int
    {
        return $this->resource['original_price']->getAmount();
    }

    public function getConvertedPrice(): string
    {
        return $this->resource['converted_price']->displayAmount() . ' ' . $this->resource['converted_price']->getCurrency()->symbol();
    }

    public function getConvertedPriceInternal(): int
    {
        return $this->resource['converted_price']->getAmount();
    }

    public function getCurrencySymbol(): string
    {
        return $this->resource['converted_price']->getCurrency()->symbol();
    }
}
