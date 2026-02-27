<?php
namespace App\Application\UseCases\Customer;

use App\Domain\Customer\Exceptions\CustomerNotFoundException;
use App\Domain\User\Exceptions\UnauthorizedException;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\Customer\VO\CustomerId;
use App\Domain\Shared\Interfaces\LoggerInterface;
use App\Domain\User\VO\UserId;

class DeleteCustomerUseCase
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private LoggerInterface $logger
    ) { }

    public function execute(CustomerId $customerId, UserId $userId): void
    {
        $customer = $this->customerRepository->findById($customerId);

        if (!$customer) {
            throw new CustomerNotFoundException();
        }

        if (!$customer->userId()->equals($userId)) {
            $this->logger->warning('Tentativa de exclusão de cliente por usuário não autorizado', [
                'customer_id' => $customerId,
                'user_id' => $userId,
            ]);

            throw new UnauthorizedException();
        }

        $this->customerRepository->delete($customer);
    }
}
