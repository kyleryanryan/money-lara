<?php

namespace App\Http\Responses;

use Illuminate\Http\Resources\Json\JsonResource;

class CalculateInstallmentsResponse extends JsonResource
{
    public function toArray($request)
    {
        return [
            'total' => $this->getTotal(),
            'installments' => $this->getInstallments(),
            'installmentBreakdown' => $this->getInstallmentBreakdown(),
        ];
    }

    public function getTotal(): string
    {
        return $this->resource['total']->displayAmount() . ' ' . $this->resource['total']->getCurrency()->symbol();
    }

    public function getInstallments(): int
    {
        return $this->resource['installments'];
    }

    public function getInstallmentBreakdown(): array
    {
        return $this->resource['installmentBreakdown'];
    }
}
