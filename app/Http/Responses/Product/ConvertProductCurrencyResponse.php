<?php

namespace App\Http\Responses\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ConvertProductCurrencyResponse extends JsonResource
{
    public function toArray($request)
    {
        return [
            'originalPrice' => $this->getOriginalPrice(),
            'originalPriceInternal' => $this->getOriginalPriceInternal(),
            'convertedPrice' => $this->getConvertedPrice(),
            'convertedPriceInternal' => $this->getConvertedPriceInternal(),
            'currency' => $this->getCurrencySymbol(),
        ];
    }

    public function getOriginalPrice(): string
    {
        return $this->resource['originalPrice']->displayAmount() . ' ' . $this->resource['originalPrice']->getCurrency()->symbol();
    }

    public function getOriginalPriceInternal(): int
    {
        return $this->resource['originalPrice']->getAmount();
    }

    public function getConvertedPrice(): string
    {
        return $this->resource['convertedPrice']->displayAmount() . ' ' . $this->resource['convertedPrice']->getCurrency()->symbol();
    }

    public function getConvertedPriceInternal(): int
    {
        return $this->resource['convertedPrice']->getAmount();
    }

    public function getCurrencySymbol(): string
    {
        return $this->resource['convertedPrice']->getCurrency()->symbol();
    }
}
