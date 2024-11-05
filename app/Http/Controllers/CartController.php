<?php

namespace App\Http\Controllers;

use App\CommandBus;
use App\Commands\Cart\AddToCartCommand;
use App\Commands\Cart\AddToCartWithQuantityCommand;
use App\Commands\Cart\ApplyFlatDiscountCommand;
use App\Commands\Cart\ApplyPercentDiscountCommand;
use App\Commands\Cart\CalculateInstallmentsCommand;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\AddToCartWithQuantityRequest;
use App\Http\Requests\ApplyFlatDiscountRequest;
use App\Http\Requests\ApplyPercentDiscountRequest;
use App\Http\Requests\CalculateInstallmentsRequest;
use App\Http\Responses\AddToCartWithQuantityResponse;
use App\Http\Responses\CalculateInstallmentsResponse;
use App\Http\Responses\CartResponse;
use App\Http\Responses\DiscountedCartResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;

class CartController extends Controller
{
    public function __construct(private CommandBus $commandBus){}

    /**
     * Money object addition calculation
     *
     * @param AddToCartRequest $request
     * @return CartResponse|JsonResponse
     */
    public function addToCart(AddToCartRequest $request): CartResponse|JsonResponse
    {
        $command = new AddToCartCommand(
            productIds: $request->getProductIds()
        );

        try {
            return $this->commandBus->handle($command);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Money object subtraction calculation
     *
     * @param ApplyFlatDiscountRequest $request
     * @return DiscountedCartResponse|JsonResponse
     */
    public function applyFlatDiscount(ApplyFlatDiscountRequest $request): DiscountedCartResponse|JsonResponse
    {
        $command = new ApplyFlatDiscountCommand(
            productIds: $request->getProductIds(),
            discount: $request->getDiscount()
        );

        try {
            return $this->commandBus->handle($command);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Money object percentage calculation
     *
     * @param ApplyPercentDiscountRequest $request
     * @return DiscountedCartResponse|JsonResponse
     */
    public function applyPercentDiscount(ApplyPercentDiscountRequest $request): DiscountedCartResponse|JsonResponse
    {
        $command = new ApplyPercentDiscountCommand(
            productIds: $request->getProductIds(),
            discountPercentage: $request->getDiscountPercentage()
        );

        try {
            return $this->commandBus->handle($command);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Money object multiplication calculation
     *
     * @param AddToCartWithQuantityRequest $request
     * @return AddToCartWithQuantityResponse|JsonResponse
     */
    public function addToCartWithQuantity(AddToCartWithQuantityRequest $request,): AddToCartWithQuantityResponse|JsonResponse
    {

        $command = new AddToCartWithQuantityCommand(
            $request->getProductId(),
            $request->getQuantity()
        );

        try {
            return $this->commandBus->handle($command);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Money object division calculation
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function calculateInstallments(CalculateInstallmentsRequest $request): CalculateInstallmentsResponse|JsonResponse
    {
        $command = new CalculateInstallmentsCommand(
            productIds: $request->getProductIds(),
            installments: $request->getInstallments()
        );

        try {
            return $this->commandBus->handle($command);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
