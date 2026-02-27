<?php

use App\Domain\Customer\Exceptions\InvalidZipcodeException;
use App\Domain\Customer\Exceptions\UnauthorizedException;
use App\Domain\Shared\Interfaces\LoggerInterface;
use App\Infrastructure\Shared\LaravelLogger;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
            app(LoggerInterface::class)->error('CEP inválido durante criação de cliente', [
                'error' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            return response()->json([
                'message' => $e->getMessage(),
                'error' => 'invalid_zipcode',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        $exceptions->render(function (NotFoundHttpException $e, $request) {
            app(LoggerInterface::class)->error('Recurso não encontrado', [
                'error' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            return response()->json([
                'message' => 'Recurso não encontrado',
                'error' => 'not_found',
            ], Response::HTTP_NOT_FOUND);
        });

        $exceptions->render(function (UnauthorizedException $e, $request) {
            $user = $request->user();

            app(LoggerInterface::class)->error('Acesso não autorizado', [
                'user_id' => $user?->id,
                'user_email' => $user?->email,
                'route' => $request->path(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'resource_id' => $request->route('customerId'),
                'error' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            return response()->json([
                'message' => $e->getMessage(),
                'error' => 'unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        });
    })->create();
