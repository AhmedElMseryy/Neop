<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(protected UserService $userService)
    {
    }

    #-------------------------------------------------------INDEX
    public function index(): JsonResponse
    {
        $users = $this->userService->getAllUsers();

        return $this->successResponse(
            data: UserResource::collection($users),
            message: 'Users retrieved successfully'
        );
    }

    #-------------------------------------------------------STORE
    public function store(CreateUserRequest $request): JsonResponse
    {
        $user = $this->userService->createUser($request->validated());

        return $this->successResponse(
            data: new UserResource($user),
            message: 'User created successfully',
            code: 201
        );
    }

    #-------------------------------------------------------SHOW
    public function show(int $id): JsonResponse
    {
        $user = $this->userService->getUserWithAccounts($id);

        if (!$user) {
            return $this->errorResponse(
                message: 'User not found',
                code: 404
            );
        }

        return $this->successResponse(
            data: new UserResource($user),
            message: 'User retrieved successfully'
        );
    }

    #-------------------------------------------------------UPDATE
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $this->userService->updateUser($id, $request->validated());

        $user = $this->userService->getUserWithAccounts($id);

        return $this->successResponse(
            data: new UserResource($user),
            message: 'User updated successfully'
        );
    }

    #-------------------------------------------------------DESTROY
    public function destroy(int $id): JsonResponse
    {
        $this->userService->deleteUser($id);

        return $this->successResponse(
            message: 'User deleted successfully'
        );
    }

}
