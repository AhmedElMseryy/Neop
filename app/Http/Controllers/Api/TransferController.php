<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransferMoneyRequest;
use App\Http\Resources\TransactionResource;
use App\Services\TransferService;
use Illuminate\Http\JsonResponse;

class TransferController extends Controller
{
    public function __construct(protected TransferService $transferService)
    {
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
        } catch (\DomainException $e) {
            // Business validation errors (from Chain)
            return $this->errorResponse(
                message: $e->getMessage(),
                code: 422
            );
        } catch (\Exception $e) {
            // Unexpected / system errors
            return $this->errorResponse(
                data: config('app.debug') ? $e->getMessage() : null,
                message: 'Transfer failed',
                code: 400
            );
        }
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
                data: config('app.debug') ? $e->getMessage() : null,
                message: 'Failed to fetch transaction history',
                code: 400
            );
        }
    }
}
