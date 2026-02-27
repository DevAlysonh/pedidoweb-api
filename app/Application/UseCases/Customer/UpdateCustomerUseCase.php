<?php
namespace App\Application\UseCases\Customer;

use App\Application\Dto\Customer\UpdateCustomerDto;
use App\Application\Shared\Traits\DiffTrait;
use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\Exceptions\CustomerNotFoundException;
use App\Domain\User\Exceptions\UnauthorizedException;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\Customer\VO\CustomerId;
use App\Domain\Shared\Interfaces\LoggerInterface;
use App\Domain\User\VO\UserId;

class UpdateCustomerUseCase
{
    use DiffTrait;

    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private LoggerInterface $logger
    ) { }

    public function execute(
        CustomerId $customerId,
        UserId $userId,
        UpdateCustomerDto $dto
    ): Customer {
        $customer = $this->customerRepository->findById($customerId);

        if(!$customer) {
            throw new CustomerNotFoundException();
        }

        if(!$customer->userId()->equals($userId)) {
            $this->logger->warning('Tentativa de atualização de cliente por usuário não autorizado', [
                'customer_id' => $customerId,
                'user_id' => $userId,
            ]);

            throw new UnauthorizedException();
        }

        $old = $customer->snapshot();

        $customer->update($dto->name, $dto->email);
        $this->customerRepository->save($customer);
        $new = $customer->snapshot();

        $this->logger->info('Os dados do cliente foram atualizados', [
            'customer_id' => $customerId,
            'user_id' => $userId,
            'changes' => $this->diff($old, $new),
        ]);

        return $customer;
    }
}
