<?php

namespace App\Http\Controllers;

use App\Application\Dto\User\LoginUserDTO;
use App\Application\Dto\User\RegisterUserDTO;
use App\Application\UseCases\User\LoginUseCase;
use App\Application\UseCases\User\MeUseCase;
use App\Application\UseCases\User\RegisterUseCase;
use App\Domain\User\VO\UserId;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\AuthTokenResource;
use App\Http\Resources\Auth\UserResource;
use App\Infrastructure\Persistence\Eloquent\Models\User as UserModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function register(
        RegisterRequest $request,
        RegisterUseCase $registerUseCase
    ): JsonResponse {
        $registerUserDTO = RegisterUserDTO::fromRequest($request->validated());

        $registerUseCase->execute($registerUserDTO);

        $eloquentUser = UserModel::where(
            'email',
            $request->input('email')
        )->firstOrFail();
        
        $token = auth('api')->fromUser($eloquentUser);

        return (new AuthTokenResource($token))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function login(
        LoginRequest $request,
        LoginUseCase $loginUseCase
    ): JsonResponse {
        $loginUserDTO = LoginUserDTO::fromRequest($request->validated());

        $token = $loginUseCase->execute($loginUserDTO);

        return (new AuthTokenResource($token))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function me(MeUseCase $meUseCase): JsonResponse
    {
        $userId = UserId::fromString(auth()->id());
        $user = $meUseCase->execute($userId);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return response()->json([
            'message' => 'Successfully logged out'
        ], Response::HTTP_OK);
    }
}
