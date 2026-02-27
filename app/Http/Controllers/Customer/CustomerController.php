<?php

namespace App\Http\Controllers\Customer;

use App\Application\Dto\Customer\CreateCustomerDTO;
use App\Application\UseCases\Customer\CreateCustomerUseCase;
use App\Application\UseCases\Customer\ListCustomersUseCase;
use App\Application\UseCases\Customer\ShowCustomerUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CreateCustomerRequest;
use App\Http\Resources\Customer\CustomerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CustomerController extends Controller
{
    public function index(
        ListCustomersUseCase $listCustomersUseCase
    ): JsonResponse {
        $customers = $listCustomersUseCase->execute(auth()->user()->id);

        return response()->json(
            CustomerResource::collection($customers),
            Response::HTTP_OK
        );
    }

    public function store(
        CreateCustomerRequest $request,
        CreateCustomerUseCase $createCustomerUseCase
    ): JsonResponse {
        $createCustomerDto = CreateCustomerDTO::fromRequest($request->validated());

        $createdCustomer = $createCustomerUseCase->execute(
            auth()->user()->id,
            $createCustomerDto
        );

        return (new CustomerResource($createdCustomer))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(
        string $customerId,
        ShowCustomerUseCase $showCustomerUseCase
    ): JsonResponse {
        $customer = $showCustomerUseCase->execute($customerId, auth()->id());

        return new CustomerResource($customer)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }
}
