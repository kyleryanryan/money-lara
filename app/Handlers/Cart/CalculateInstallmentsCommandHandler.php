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
    public function __invoke(CalculateInstallmentsCommand $command): CalculateInstallmentsResponse
    {
        $products = Product::whereIn('id', $command->getProductIds())->get();

        if ($products->isEmpty()) {
            throw new InvalidArgumentException('No valid products found.');
        }

        $firstProduct = $products->first();
        $currency = Currency::from($firstProduct->currency);
        $totalMoney = Money::fromScaledInt(0, $currency);

        foreach ($products as $product) {
            if($product->currency !== $firstProduct->currency){
                throw new InvalidArgumentException('All products must have the same currency.');
            }
            $totalMoney = $totalMoney->add($product->getMoney());
        }
        $termMoney = Money::fromFloat($command->getInstallments(), $currency);
        $baseInstallmentMoney = $totalMoney->divide($termMoney);

        $finalBaseMoney = Money::fromFloat((float)$baseInstallmentMoney->displayAmount(), $currency);
        $totalBaseInstallment = $finalBaseMoney->multiply($termMoney);
        $remainder = $totalMoney->subtract($totalBaseInstallment);

        $installments = [];
        for ($i = 0; $i < $command->getInstallments(); $i++) {
            if(($command->getInstallments() - 1) === $i){
                $installment = $finalBaseMoney->add($remainder);
                $installments[] = $installment->displayAmount() . ' ' . $currency->symbol();
            }
            else{
                $installments[] = $baseInstallmentMoney->displayAmount() . ' ' . $currency->symbol();
            }
        }
 
        return new CalculateInstallmentsResponse([
            'total' => $totalMoney,
            'installments' => $command->getInstallments(),
            'installmentBreakdown' => $installments,
        ]);
    }
}
