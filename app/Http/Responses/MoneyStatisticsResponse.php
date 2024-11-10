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
        return $this->resource['total']->formatAmountWithSymbol();
    }

    public function getLowest(): string
    {
        return $this->resource['lowest']->formatAmountWithSymbol();
    }

    public function getHighest(): string
    {
        return $this->resource['highest']->formatAmountWithSymbol();
    }

    public function getAverage(): string
    {
        return $this->resource['average']->formatAmountWithSymbol();
    }
}
