<?php

namespace App\Http\Controllers\Customer;

use App\Application\Dto\Customer\CreateCustomerDTO;
use App\Application\UseCases\Customer\CreateCustomerUseCase;
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

        $createdCustomer = $createCustomerUseCase->execute($createCustomerDto);

        return response()->json([
            'message' => 'Cliente criado com sucesso',
            'customer' => [
                'id' => $createdCustomer->id(),
                'name' => $createdCustomer->name(),
                'email' => $createdCustomer->email(),
                'address' => [
                    'street' => $createdCustomer->address()->street(),
                    'number' => $createdCustomer->address()->number(),
                    'city' => $createdCustomer->address()->city(),
                    'state' => $createdCustomer->address()->state(),
                    'zipcode' => $createdCustomer->address()->zipcode(),
                ],
            ],
        ], Response::HTTP_CREATED);
    }
}
