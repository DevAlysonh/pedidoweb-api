<?php

use App\Domain\Customer\Exceptions\InvalidZipcodeException;
use App\Infrastructure\Shared\LaravelLogger;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (InvalidZipcodeException $e, $request) {
            new LaravelLogger()->error('CEP inválido durante criação de cliente', [
                'error' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            return response()->json([
                'message' => $e->getMessage(),
                'error' => 'invalid_zipcode',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        $exceptions->render(function (Throwable $e, $request) {
            new LaravelLogger()->error('Um erro interno inesperado ocorreu', [
                'error' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            return response()->json([
                'message' => 'Um erro interno inesperado ocorreu. Por favor, tente novamente mais tarde.',
                'error' => 'internal_server_error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    })->create();
