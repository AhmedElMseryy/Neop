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
        $account = $this->accountService->createAccount($request->validated());

        return $this->successResponse(
            data: new AccountResource($account),
            message: 'Account created successfully',
            code: 201
        );
    }

    #-------------------------------------------------------SHOW
    public function show(int $id): JsonResponse
    {
        $account = $this->accountService->getAccountById($id);

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
        $this->accountService->updateAccount($id, $request->validated());

        $account = $this->accountService->getAccountById($id);

        return $this->successResponse(
            data: new AccountResource($account),
            message: 'Account updated successfully'
        );
    }

    #-------------------------------------------------------ACTIVATE
    public function activate(int $id): JsonResponse
    {
        $this->accountService->activateAccount($id);

        $account = $this->accountService->getAccountById($id);

        return $this->successResponse(
            data: new AccountResource($account),
            message: 'Account activated successfully'
        );
    }
}
