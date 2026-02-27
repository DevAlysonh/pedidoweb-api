<?php

namespace App\Http\Controllers\Customer;

use App\Application\Dto\Customer\UpdateCustomerDto;
use App\Application\Dto\Customer\CreateCustomerDTO;
use App\Application\UseCases\Customer\CreateCustomerUseCase;
use App\Application\UseCases\Customer\ListCustomersUseCase;
use App\Application\UseCases\Customer\ShowCustomerUseCase;
use App\Application\UseCases\Customer\DeleteCustomerUseCase;
use App\Application\UseCases\Customer\UpdateCustomerUseCase;
use App\Domain\Customer\VO\CustomerId;
use App\Domain\User\VO\UserId;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CreateCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Resources\Customer\CustomerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CustomerController extends Controller
{
    public function index(
        ListCustomersUseCase $listCustomersUseCase
    ): JsonResponse {
        $customers = $listCustomersUseCase->execute($this->authUserId());

        return CustomerResource::collection($customers)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function store(
        CreateCustomerRequest $request,
        CreateCustomerUseCase $createCustomerUseCase
    ): JsonResponse {
        $createCustomerDto = CreateCustomerDTO::fromRequest($request->validated());

        $createdCustomer = $createCustomerUseCase->execute(
            UserId::fromString($this->authUserId()),
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
        $customer = $showCustomerUseCase->execute(
            CustomerId::fromString($customerId),
            UserId::fromString($this->authUserId())
        );

        return new CustomerResource($customer)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function update(
        string $customerId,
        UpdateCustomerRequest $request,
        UpdateCustomerUseCase $updateCustomerUseCase
    ): JsonResponse {
        $dto = UpdateCustomerDto::fromRequest($request->validated());

        $updated = $updateCustomerUseCase->execute(
            CustomerId::fromString($customerId),
            $this->authUserId(),
            $dto
        );

        return new CustomerResource($updated)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function destroy(
        string $customerId,
        DeleteCustomerUseCase $deleteCustomerUseCase
    ): JsonResponse {
        $deleteCustomerUseCase->execute(CustomerId::fromString($customerId), $this->authUserId());

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    private function authUserId(): UserId
    {
        return UserId::fromString(auth()->id());
    }
}
