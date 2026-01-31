<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransferMoneyRequest;
use App\Services\TransferService;
use App\Traits\ApiResponse;
use App\Http\Resources\TransactionResource;
use Illuminate\Http\JsonResponse;

class TransferController extends Controller
{

    protected TransferService $transferService;

    public function __construct(TransferService $transferService)
    {
        $this->transferService = $transferService;
    }

    #-------------------------------------------------------TRANSFER
    public function transfer(TransferMoneyRequest $request): JsonResponse
    {
        try {
            $transaction = $this->transferService->transfer(
                sourceAccountId: $request->source_account_id,
                destinationAccountId: $request->destination_account_id,
                amount: $request->amount,
                description: $request->description
            );

            return $this->successResponse(
                data: new TransactionResource($transaction),
                message: 'Transfer completed successfully',
                code: 201
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: 'Transfer failed',
                data: $e->getMessage(),
                code: 400
            );
        }
    }

    #-------------------------------------------------------VALIDATE TRANSFER
    public function validateTransfer(TransferMoneyRequest $request): JsonResponse
    {
        $validation = $this->transferService->validateTransfer(
            $request->source_account_id,
            $request->destination_account_id,
            $request->amount
        );

        return $this->successResponse(
            data: $validation,
            message: $validation['valid'] ? 'Transfer is valid' : 'Transfer validation failed'
        );
    }

    #-------------------------------------------------------GET BY REFERENCE
    public function getByReference(string $reference): JsonResponse
    {
        $transaction = $this->transferService->getTransactionByReference($reference);

        if (!$transaction) {
            return $this->errorResponse(
                message: 'Transaction not found',
                code: 404
            );
        }

        return $this->successResponse(
            data: new TransactionResource($transaction),
            message: 'Transaction retrieved successfully'
        );
    }

    #-------------------------------------------------------ACCOUNT HISTORY
    public function getAccountHistory(int $accountId): JsonResponse
    {
        try {
            $transactions = $this->transferService->getAccountTransactionHistory($accountId);

            return $this->successResponse(
                data: TransactionResource::collection($transactions),
                message: 'Transaction history retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: 'Failed to fetch transaction history',
                data: $e->getMessage()
            );
        }
    }
}
