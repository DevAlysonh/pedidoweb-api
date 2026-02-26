<?php

namespace App\Infrastructure\Shared;

use App\Domain\Shared\Interfaces\LoggerInterface;
use Illuminate\Support\Facades\Log;

class LaravelLogger implements LoggerInterface
{
    public function info(string $message, array $context = []): void
    {
        Log::channel('daily')->info($message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        Log::channel('daily')->warning($message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        Log::channel('daily')->error($message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        Log::channel('daily')->debug($message, $context);
    }
}