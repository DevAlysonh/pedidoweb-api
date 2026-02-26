<?php

declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Application\Dto\CepData;

interface CepService
{
    /**
     * Busca dados de endereço a partir do CEP
     *
     * @param string $zipcode CEP no formato 8 dígitos (sem formatação)
     * @return CepData|null Dados do endereço ou null se não encontrado
     * @throws \Exception Se houver erro na chamada da API
     */
    public function lookup(string $zipcode): ?CepData;
}
