<?php

namespace App\Http\Controllers\Customer;

use App\Application\Dto\Customer\CreateCustomerDTO;
use App\Application\UseCases\Customer\CreateCustomerUseCase;
use App\Application\UseCases\Customer\ListCustomersUseCase;
use App\Application\UseCases\Customer\ShowCustomerUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CreateCustomerRequest;
use App\Http\Requests\Customer\ShowCustomerRequest;
use App\Http\Resources\Customer\CustomerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CustomerController extends Controller
{
    public function index(
        ListCustomersUseCase $listCustomersUseCase
    ): JsonResponse {
        return response()->json([
            'message' => auth()->user()->id
        ]);
        $customers = $listCustomersUseCase->execute(auth()->user()->id);

        return response()->json([
            'customers' => CustomerResource::collection($customers)
        ]);
    }

    public function store(
        CreateCustomerRequest $request,
        CreateCustomerUseCase $createCustomerUseCase
    ): JsonResponse {
        $createCustomerDto = CreateCustomerDTO::fromRequest($request->validated());

        $createdCustomer = $createCustomerUseCase->execute($createCustomerDto);

        return (new CustomerResource($createdCustomer))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(
        ShowCustomerRequest $request,
        ShowCustomerUseCase $showCustomerUseCase
    ): JsonResponse {
        $customerId = $request->input('customer_id');
        $customer = $showCustomerUseCase->execute($customerId);

        if (!$customer) {
            return response()->json(
                ['message' => 'Cliente não encontrado'],
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json([
            'customer' => new CustomerResource($customer)
        ]);
    }
}
