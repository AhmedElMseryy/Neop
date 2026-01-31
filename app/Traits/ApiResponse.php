<?php

namespace App\Traits;

trait ApiResponse
{
    #-------------------------------------------------------SUCCESS RESPONSE
    public function successResponse(mixed $data = null, string $message = '', int $code = 200, array $additional = [], array $headers = [])
    {
        return $this->apiResource(data: $data, message: $message, code: $code, additional: $additional, headers: $headers);
    }

    #-------------------------------------------------------API RESOURCE RESPONSE
    public function apiResource(mixed $data = null, bool $status = true, string $message = '', int $code = 200, array $additional = [], array $headers = [])
    {
        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ] + $additional;
        return response()->json($response, $code, $headers);
    }

    #-------------------------------------------------------ERROR RESPONSE
    public function errorResponse(mixed $data = null, string $message = '', int $code = 422, array $headers = [])
    {
        return $this->apiResource(status: false, message: $message, code: $code, additional: ['errors' => $data], headers: $headers);
    }

    #-------------------------------------------------------PAGINATE RESPONSE
    public function paginateResponse($data, $collection, $message = '', $additional = [])
    {
        $meta = [
            'meta' => [
                'total' => $collection->total(),
                'from' => $collection->firstItem(),
                'to' => $collection->lastItem(),
                'count' => $collection->count(),
                'per_page' => $collection->perPage(),
                'current_page' => $collection->currentPage(),
                'last_page' => $collection->lastPage()
            ],
        ];
        return $this->apiResource(data: $data, message: $message, additional: $meta);
    }
}
