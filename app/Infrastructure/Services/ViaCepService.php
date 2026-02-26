<?php

declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Application\Dto\CepData;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class ViaCepService implements CepService
{
    private const VIACEP_URL = 'https://viacep.com.br/ws';

    /**
     * Busca dados de endereço a partir do CEP usando ViaCEP
     *
     * @param string $zipcode CEP no formato 8 dígitos (com ou sem formatação)
     * @return CepData|null
     * @throws \Exception
     */
    public function lookup(string $zipcode): ?CepData
    {
        // Normalizar o CEP (remover formatação)
        $cleanZipcode = $this->cleanZipcode($zipcode);
        
        // Validar formato do CEP (8 dígitos)
        if (!$this->isValidZipcode($cleanZipcode)) {
            throw new \InvalidArgumentException("CEP deve conter 8 dígitos. Recebido: {$zipcode}");
        }

        try {
            $response = Http::timeout(5)
                ->get("{$this->VIACEP_URL}/{$cleanZipcode}/json");

            if (!$response->successful()) {
                throw new \Exception("Erro ao consultar ViaCEP: {$response->status()}");
            }

            $data = $response->json();

            // ViaCEP retorna {'erro': true} quando CEP não existe
            if (isset($data['erro']) && $data['erro'] === true) {
                return null;
            }

            return new CepData(
                zipcode: $this->formatZipcode($cleanZipcode),
                street: $data['logradouro'] ?? '',
                city: $data['localidade'] ?? '',
                state: $data['uf'] ?? '',
            );
        } catch (ConnectionException $e) {
            throw new \Exception("Erro de conexão ao consultar ViaCEP: {$e->getMessage()}", 0, $e);
        }
    }

    private function cleanZipcode(string $zipcode): string
    {
        return preg_replace('/\D/', '', $zipcode);
    }

    private function isValidZipcode(string $zipcode): bool
    {
        return strlen($zipcode) === 8 && ctype_digit($zipcode);
    }

    private function formatZipcode(string $zipcode): string
    {
        return substr($zipcode, 0, 5) . '-' . substr($zipcode, 5);
    }
}
