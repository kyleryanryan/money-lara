<?php

namespace App\Handlers\Cart;

use App\Commands\Cart\CalculateInstallmentsCommand;
use App\Models\Product;
use App\Services\Money\Money;
use App\Support\NumberHelper;
use App\Enums\Currency;
use App\Http\Responses\CalculateInstallmentsResponse;
use InvalidArgumentException;

class CalculateInstallmentsCommandHandler
{
    public function handle(CalculateInstallmentsCommand $command): CalculateInstallmentsResponse
    {
        $products = Product::whereIn('id', $command->getProductIds())->get();

        if ($products->isEmpty()) {
            throw new InvalidArgumentException('No valid products found.');
        }

        $firstProduct = $products->first();
        $currency = Currency::from($firstProduct->currency);
        $totalMoney = new Money(0, $currency);

        foreach ($products as $product) {
            $totalMoney = $totalMoney->add($product->getMoney());
        }
        $termInSmallestUnit = NumberHelper::floatToInt($command->getInstallments(), Money::STORAGE_PRECISION);

        $termMoney = new Money($termInSmallestUnit, $currency);
        $baseInstallmentMoney = $totalMoney->divide($termMoney);
        $totalBaseInstallment = $baseInstallmentMoney->multiply($termMoney);
        $remainder = $totalMoney->subtract($totalBaseInstallment);

        $installments = [];
        for ($i = 0; $i < $command->getInstallments(); $i++) {
            $installment = $i === $command->getInstallments() - 1
                ? $baseInstallmentMoney->add($remainder)
                : $baseInstallmentMoney;

            $installments[] = $installment->displayAmount() . ' ' . $currency->symbol();
        }

 
        return new CalculateInstallmentsResponse([
            'total' => $totalMoney,
            'installments' => $command->getInstallments(),
            'installmentBreakdown' => $installments,
        ]);
    }
}
