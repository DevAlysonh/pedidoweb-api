<?php

declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Application\Dto\CepData;
use App\Domain\Customer\Exceptions\InvalidZipcodeException;
use Illuminate\Support\Facades\Http;

class ViaCepService implements CepService
{
    public function lookup(string $cep): CepData
    {
        $response = Http::get("https://viacep.com.br/ws/{$cep}/json/");

        if ($response->failed() || isset($response['erro'])) {
            throw new InvalidZipcodeException("CEP inválido");
        }

        return new CepData(
            street: $response['logradouro'] ?? '',
            number: $response['numero'] ?? '',
            city: $response['localidade'] ?? '',
            state: $response['uf'] ?? '',
            zipcode: $response['cep'] ?? ''
        );
    }
}
