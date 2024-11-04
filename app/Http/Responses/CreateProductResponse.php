<?php

namespace App\Http\Responses;

use Illuminate\Http\Resources\Json\JsonResource;

class CreateProductResponse extends JsonResource
{
    public function toArray($request)
    {
        return [
            'product' => $this->getProduct(),
            'price' => $this->getPrice(),
            'price_internal' => $this->getPriceInternal(),
            'currency' => $this->getCurrency(),
            'message' => $this->getMessage(),
        ];
    }

    /**
     * Get the product details.
     *
     * @return array{id: string, name: string, created_at: string, updated_at: string}
     */
    public function getProduct(): array
    {
        return [
            'id' => $this->resource['product']->id,
            'name' => $this->resource['product']->name,
            'created_at' => $this->resource['product']->created_at,
            'updated_at' => $this->resource['product']->updated_at,
        ];
    }

    public function getPrice(): string
    {
        return $this->resource['money']->displayAmount();
    }

    public function getCurrency(): ?string
    {
        return $this->resource['money']->getCurrency()->symbol();
    }

    public function getMessage(): string
    {
        return 'Product created successfully';
    }

    public function getPriceInternal(): int
    {
        return $this->resource['money']->getAmount();
    }
}
