<?php

namespace App\Handlers\Cart;

use App\Commands\Cart\AddToCartWithQuantityCommand;
use App\Http\Responses\AddToCartWithQuantityResponse;
use App\Models\Product;
use App\Services\Money\Money;
use App\Support\NumberHelper;
use InvalidArgumentException;

class AddToCartWithQuantityCommandHandler
{
    public function handle(AddToCartWithQuantityCommand $command): AddToCartWithQuantityResponse
    {
        $product = Product::findOrFail($command->getProductId());

        /** @var Money $productMoney */
        $productMoney = $product->getMoney();
        $quantityInSmallestUnit = NumberHelper::floatToInt($command->getQuantity(), Money::STORAGE_PRECISION);

        try {
            $totalMoney = $productMoney->multiply(new Money($quantityInSmallestUnit, $productMoney->getCurrency()));

        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException('Error calculating total.');
        }

        return new AddToCartWithQuantityResponse([
            'product' => $product,
            'unit_price' => $productMoney,
            'quantity' => $command->getQuantity(),
            'total_price' => $totalMoney,
        ]);
    }
}
