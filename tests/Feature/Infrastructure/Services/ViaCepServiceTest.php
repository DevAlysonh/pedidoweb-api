<?php

namespace Tests\Feature\Infrastructure\Services;

use App\Application\Dto\CepData;
use App\Domain\Customer\Exceptions\InvalidZipcodeException;
use App\Infrastructure\Services\ViaCepService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ViaCepServiceTest extends TestCase
{
    public function testLookupReturnsCepData()
    {
        Http::fake([
            'https://viacep.com.br/ws/01001000/json/' => Http::response([
                'logradouro' => 'Praça da Sé',
                'numero' => '',
                'localidade' => 'São Paulo',
                'uf' => 'SP',
                'cep' => '01001-000'
            ], 200)
        ]);

        $service = new ViaCepService();
        $cepData = $service->lookup('01001000');

        $this->assertInstanceOf(CepData::class, $cepData);
        $this->assertEquals('Praça da Sé', $cepData->street);
        $this->assertEquals('São Paulo', $cepData->city);
        $this->assertEquals('SP', $cepData->state);
        $this->assertEquals('01001-000', $cepData->zipcode);
    }

    public function testLookupThrowsInvalidZipcodeException()
    {
        Http::fake([
            'https://viacep.com.br/ws/01001000/json/' => Http::response(
                ['erro' => true]
            ),
        ]);;

        $service = new ViaCepService();

        $this->expectException(InvalidZipcodeException::class);
        $service->lookup('00000000');
    }
}
