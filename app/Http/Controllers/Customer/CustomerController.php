<?php

namespace App\Http\Controllers\Customer;

use App\Application\Dto\Customer\CreateCustomer;
use App\Application\UseCases\Customer\CreateUser;
use App\Domain\Customer\Exceptions\InvalidZipcodeException;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CustomerController extends Controller
{
    public function store(Request $request, CreateUser $createUserUseCase): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:customers',
                'street' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'number' => 'required|string|max:20',
                'zipcode' => 'required|regex:/^\d{5}-?\d{3}$/',
            ]);

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
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => 'invalid_input',
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao consultar CEP. Tente novamente mais tarde.',
                'error' => 'cep_service_error',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }
}
