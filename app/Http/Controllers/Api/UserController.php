<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
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
        try {
            $user = $this->userService->createUser($request->validated());

            return $this->successResponse(
                data: new UserResource($user),
                message: 'User created successfully',
                code: 201
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: 'Failed to create user',
                data: $e->getMessage(),
                code: 500
            );
        }
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
        try {
            $updated = $this->userService->updateUser($id, $request->validated());

            if (!$updated) {
                return $this->errorResponse(
                    message: 'User not found',
                    code: 404
                );
            }

            $user = $this->userService->getUserWithAccounts($id);

            return $this->successResponse(
                data: new UserResource($user),
                message: 'User updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: 'Failed to update user',
                data: $e->getMessage(),
                code: 500
            );
        }
    }

    #-------------------------------------------------------DESTROY
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->userService->deleteUser($id);

            if (!$deleted) {
                return $this->errorResponse(
                    message: 'User not found',
                    code: 404
                );
            }

            return $this->successResponse(
                message: 'User deleted successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: 'Failed to delete user',
                data: $e->getMessage(),
                code: 500
            );
        }
    }
}
