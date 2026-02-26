<?php

namespace App\Http\Controllers\Customer;

use App\Application\Dto\Customer\CreateCustomer;
use App\Application\UseCases\Customer\CreateUser;
use App\Domain\Customer\Exceptions\InvalidZipcodeException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CreateCustomerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CustomerController extends Controller
{
    public function store(CreateCustomerRequest $request, CreateUser $createUserUseCase): JsonResponse
    {
        try {
            $validated = $request->validated();

            $createCustomerDto = new CreateCustomer(
                name: $validated['name'],
                email: $validated['email'],
                street: $validated['street'],
                number: $validated['number'],
                city: $validated['city'] ?? '',
                state: $validated['state'] ?? '',
                zipcode: $validated['zipcode'],
            );

            $createUserUseCase->execute($createCustomerDto);

            return response()->json([
                'message' => 'Cliente criado com sucesso',
            ], Response::HTTP_CREATED);
        } catch (InvalidZipcodeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => 'invalid_zipcode',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao consultar CEP. Tente novamente mais tarde.',
                'error' => 'cep_service_error',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }
}
