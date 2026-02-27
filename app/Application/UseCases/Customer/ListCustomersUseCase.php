<?php

declare(strict_types=1);

namespace App\Application\UseCases\Customer;

use App\Domain\Customer\Repositories\CustomerRepositoryInterface;

class ListCustomersUseCase
{
    public function __construct(private CustomerRepositoryInterface $repository) {}

    public function execute(string $userId): array
    {
        return $this->repository->findAllByUser($userId);
    }
}
