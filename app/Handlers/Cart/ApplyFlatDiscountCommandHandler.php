<?php

namespace App\Handlers\Cart;

use App\Commands\Cart\ApplyFlatDiscountCommand;
use App\Enums\Currency;
use App\Http\Responses\DiscountedCartResponse;
use App\Models\Product;
use App\Services\Money\Money;
use App\Support\NumberHelper;
use InvalidArgumentException;

class ApplyFlatDiscountCommandHandler
{
    public function __invoke(ApplyFlatDiscountCommand $command): DiscountedCartResponse
    {
        $productIds = $command->getProductIds();
        $discount = $command->getDiscount();
        
        $products = Product::whereIn('id', $productIds)->get();

        if ($products->isEmpty()) {
            throw new InvalidArgumentException('No valid products found.');
        }

        $firstProduct = $products->first();
        $currency = Currency::from($firstProduct->currency);
        $totalMoney = Money::fromInt(0, $currency);

        $productList = [];

        foreach ($products as $product) {
            if (Currency::from($product->currency) !== $currency) {
                throw new InvalidArgumentException('All products must have the same currency.');
            }

            $productMoney = $product->getMoney();
            $productList[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $productMoney->displayAmount() . ' ' . $productMoney->getCurrency()->symbol(),
            ];

            $totalMoney = $totalMoney->add($productMoney);
        }

        $discountMoney = Money::fromFloat($discount, $currency);
        $discountedTotal = $totalMoney->subtract($discountMoney);

        $responseData = [
            'products' => $productList,
            'subTotal' => $totalMoney,
            'total' => $discountedTotal,
            'discount' => $discountMoney->displayAmount() . ' ' . $currency->symbol(),
        ];

        return new DiscountedCartResponse($responseData);
    }
}
