<?php

namespace App\Http\Controllers\Customer;

use App\Application\Dto\Customer\CreateCustomer;
use App\Application\UseCases\Customer\CreateUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CreateCustomerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CustomerController extends Controller
{
    public function store(CreateCustomerRequest $request, CreateUser $createUserUseCase): JsonResponse
    {
        $createCustomerDto = CreateCustomer::fromRequest($request->validated());

        $createUserUseCase->execute($createCustomerDto);

        return response()->json([
            'message' => 'Cliente criado com sucesso',
        ], Response::HTTP_CREATED);
    }
}
