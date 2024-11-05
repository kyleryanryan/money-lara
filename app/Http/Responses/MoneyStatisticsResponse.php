<?php

namespace App\Http\Responses;

use Illuminate\Http\Resources\Json\JsonResource;

class MoneyStatisticsResponse extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'total' => $this->getTotal(),
            'lowest' => $this->getLowest(),
            'highest' => $this->getHighest(),
            'average' => $this->getAverage(),
        ];
    }

    public function getTotal(): string
    {
        return $this->resource['total']->displayAmount() . ' ' . $this->resource['total']->getCurrency()->symbol();
    }

    public function getLowest(): string
    {
        return $this->resource['lowest']->displayAmount() . ' ' . $this->resource['lowest']->getCurrency()->symbol();
    }

    public function getHighest(): string
    {
        return $this->resource['highest']->displayAmount() . ' ' . $this->resource['highest']->getCurrency()->symbol();
    }

    public function getAverage(): string
    {
        return $this->resource['average']->displayAmount() . ' ' . $this->resource['average']->getCurrency()->symbol();
    }
}
