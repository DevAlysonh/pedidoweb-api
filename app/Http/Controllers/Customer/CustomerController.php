<?php

namespace App\Http\Controllers\Customer;

use App\Application\Dto\Customer\CreateCustomerDTO;
use App\Application\UseCases\Customer\CreateCustomer as CreateCustomerUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CreateCustomerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CustomerController extends Controller
{
    public function store(
        CreateCustomerRequest $request,
        CreateCustomerUseCase $createCustomerUseCase
    ): JsonResponse {
        $createCustomerDto = CreateCustomerDTO::fromRequest($request->validated());

        $createCustomerUseCase->execute($createCustomerDto);
        return response()->json([
            'message' => 'Cliente criado com sucesso',
        ], Response::HTTP_CREATED);
    }
}
