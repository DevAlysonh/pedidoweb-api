<?php

use App\Application\Dto\CepData;
use App\Application\UseCases\Customer\CreateCustomerUseCase;
use App\Application\Dto\Customer\CreateCustomerDTO;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\Shared\Interfaces\IdGeneratorInterface;
use App\Domain\Shared\Interfaces\LoggerInterface;
use App\Domain\User\VO\UserId;
use App\Infrastructure\Services\CepService;
use PHPUnit\Framework\TestCase;

class CreateCustomerUseCaseTest extends TestCase
{
    public function testExecuteSuccess()
    {
        $dto = new CreateCustomerDTO('João', 'joao@email.com', 'Rua A', '123', 'Cidade', 'SP', '12345-678');
        $repository = $this->createMock(CustomerRepositoryInterface::class);
        $idGenerator = $this->createMock(IdGeneratorInterface::class);
        $cepService = $this->createMock(CepService::class);
        $logger = $this->createMock(LoggerInterface::class);

        $cepData = new CepData(
            zipcode: '12345-678',
            number: '123',
            street: 'Rua A',
            city: 'Cidade',
            state: 'SP',
        );

        $idGenerator->method('generate')->willReturn('cus_1');
        $cepService->method('lookup')->willReturn($cepData);

        $repository->expects($this->once())->method('save');
        $logger->expects($this->once())->method('info');

        $useCase = new CreateCustomerUseCase($repository, $idGenerator, $cepService, $logger);

        $userId = UserId::fromString('user_1');
        $customer = $useCase->execute($userId,$dto);

        $this->assertEquals('cus_1', $customer->id());
        $this->assertEquals('João', $customer->name());
    }

    public function testExecuteInvalidZipcode()
    {
        $dto = new CreateCustomerDTO('João', 'joao@email.com', 'Rua A', '123', 'Cidade', 'SP', '00000-000');
        $repository = $this->createMock(CustomerRepositoryInterface::class);
        $idGenerator = $this->createMock(IdGeneratorInterface::class);
        $cepService = $this->createMock(CepService::class);
        $logger = $this->createMock(LoggerInterface::class);

        $cepService->method('lookup')->willReturn(null);

        $useCase = new CreateCustomerUseCase($repository, $idGenerator, $cepService, $logger);

        $this->expectException(\App\Domain\Customer\Exceptions\InvalidZipcodeException::class);
        $userId = UserId::fromString('user_1');
        $useCase->execute($userId,$dto);
    }
}
