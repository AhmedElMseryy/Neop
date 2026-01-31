<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\CreateAccountRequest;
use App\Http\Requests\Account\UpdateAccountRequest;
use App\Http\Resources\AccountResource;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    public function __construct(protected AccountService $accountService)
    {
    }

    #-------------------------------------------------------INDEX
    public function index(): JsonResponse
    {
        $accounts = $this->accountService->getAllAccounts();

        return $this->successResponse(
            data: AccountResource::collection($accounts),
            message: 'Accounts retrieved successfully'
        );
    }

    #-------------------------------------------------------STORE
    public function store(CreateAccountRequest $request): JsonResponse
    {
        try {
            $account = $this->accountService->createAccount($request->validated());

            return $this->successResponse(
                data: new AccountResource($account),
                message: 'Account created successfully',
                code: 201
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                data: $e->getMessage(),
                message: 'Failed to create account',
                code: 500
            );
        }
    }

    #-------------------------------------------------------SHOW
    public function show(int $id): JsonResponse
    {
        $account = $this->accountService->getAccountById($id);

        if (!$account) {
            return $this->errorResponse(
                message: 'Account not found',
                code: 404
            );
        }

        return $this->successResponse(
            data: new AccountResource($account),
            message: 'Account retrieved successfully'
        );
    }

    #-------------------------------------------------------USER ACCOUNTS
    public function getUserAccounts(int $userId): JsonResponse
    {
        $accounts = $this->accountService->getUserAccounts($userId);

        return $this->successResponse(
            data: AccountResource::collection($accounts),
            message: 'User accounts retrieved successfully'
        );
    }

    #-------------------------------------------------------UPDATE
    public function update(UpdateAccountRequest $request, int $id): JsonResponse
    {
        try {
            $updated = $this->accountService->updateAccount($id, $request->validated());

            if (!$updated) {
                return $this->errorResponse(
                    message: 'Account not found',
                    code: 404
                );
            }

            $account = $this->accountService->getAccountById($id);

            return $this->successResponse(
                data: new AccountResource($account),
                message: 'Account updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                data: $e->getMessage(),
                message: 'Failed to update account',
                code: 500
            );
        }
    }

    #-------------------------------------------------------ACTIVATE
    public function activate(int $id): JsonResponse
    {
        try {
            $this->accountService->activateAccount($id);
            $account = $this->accountService->getAccountById($id);

            return $this->successResponse(
                data: new AccountResource($account),
                message: 'Account activated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                data: $e->getMessage(),
                message: 'Failed to activate account',
                code: 500
            );
        }
    }
}
