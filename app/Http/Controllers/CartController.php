<?php

namespace App\Http\Controllers;

use App\Commands\Cart\AddToCartCommand;
use App\Commands\Cart\AddToCartWithQuantityCommand;
use App\Commands\Cart\ApplyFlatDiscountCommand;
use App\Commands\Cart\ApplyPercentDiscountCommand;
use App\Commands\Cart\CalculateInstallmentsCommand;
use App\Handlers\Cart\AddToCartCommandHandler;
use App\Handlers\Cart\AddToCartWithQuantityCommandHandler;
use App\Handlers\Cart\ApplyFlatDiscountCommandHandler;
use App\Handlers\Cart\ApplyPercentDiscountCommandHandler;
use App\Handlers\Cart\CalculateInstallmentsCommandHandler;
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
    /**
     * Money object addition calculation
     *
     * @param AddToCartRequest $request
     * @param AddToCartCommandHandler $handler
     * @return CartResponse|JsonResponse
     */
    public function addToCart(AddToCartRequest $request, AddToCartCommandHandler $handler): CartResponse|JsonResponse
    {
        $command = new AddToCartCommand(
            productIds: $request->getProductIds()
        );

        try {
            return $handler->handle($command);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Money object subtraction calculation
     *
     * @param ApplyFlatDiscountRequest $request
     * @param ApplyFlatDiscountCommandHandler $handler
     * @return DiscountedCartResponse|JsonResponse
     */
    public function applyFlatDiscount(ApplyFlatDiscountRequest $request, ApplyFlatDiscountCommandHandler $handler): DiscountedCartResponse|JsonResponse
    {
        $command = new ApplyFlatDiscountCommand(
            productIds: $request->getProductIds(),
            discount: $request->getDiscount()
        );

        try {
            return $handler->handle($command);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Money object percentage calculation
     *
     * @param ApplyPercentDiscountRequest $request
     * @param ApplyPercentDiscountCommandHandler $handler
     * @return DiscountedCartResponse|JsonResponse
     */
    public function applyPercentDiscount(ApplyPercentDiscountRequest $request, ApplyPercentDiscountCommandHandler $handler): DiscountedCartResponse|JsonResponse
    {
        $command = new ApplyPercentDiscountCommand(
            productIds: $request->getProductIds(),
            discountPercentage: $request->getDiscountPercentage()
        );

        try {
            return $handler->handle($command);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Money object multiplication calculation
     *
     * @param AddToCartWithQuantityRequest $request
     * @param AddToCartWithQuantityCommandHandler $handler
     * @return AddToCartWithQuantityResponse|JsonResponse
     */
    public function addToCartWithQuantity(
        AddToCartWithQuantityRequest $request,
        AddToCartWithQuantityCommandHandler $handler
    ): AddToCartWithQuantityResponse|JsonResponse {

        $command = new AddToCartWithQuantityCommand(
            $request->getProductId(),
            $request->getQuantity()
        );

        try {
            return $handler->handle($command);
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
    public function calculateInstallments(CalculateInstallmentsRequest $request, CalculateInstallmentsCommandHandler $handler): CalculateInstallmentsResponse
    {
        $command = new CalculateInstallmentsCommand(
            productIds: $request->getProductIds(),
            installments: $request->getInstallments()
        );

        return $handler->handle($command);
    }
}
