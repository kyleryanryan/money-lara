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
    public function __invoke(AddToCartWithQuantityCommand $command): AddToCartWithQuantityResponse
    {
        $product = Product::findOrFail($command->getProductId());

        /** @var Money $productMoney */
        $productMoney = $product->getMoney();

        try {
            $totalMoney = $productMoney->multiply(Money::fromFloat($command->getQuantity(), $productMoney->getCurrency()));

        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException('Error calculating total.');
        }

        return new AddToCartWithQuantityResponse([
            'product' => $product,
            'unitPrice' => $productMoney,
            'quantity' => $command->getQuantity(),
            'totalPrice' => $totalMoney,
        ]);
    }
}
