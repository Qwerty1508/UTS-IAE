<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Exception;

class UserController extends Controller
{
    use ApiResponse;

    /**
     * PROVIDER: GET /api/users/{id}
     */
    public function show($id): JsonResponse
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse("Invalid User ID", 400);
        }

        $user = User::find($id);

        if (!$user) {
            return $this->errorResponse("User not found", 404);
        }

        return $this->successResponse("User found", $user);
    }

    /**
     * CONSUMER: GET /api/users/{id}/orders
     */
    public function showWithOrders($id): JsonResponse
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse("Invalid User ID", 400);
        }

        $user = User::find($id);
        if (!$user) {
            return $this->errorResponse("User not found", 404);
        }

        try {
            $baseUrl = config('services.order_service.base_url');
            $response = Http::timeout(5)->get("{$baseUrl}/api/orders", [
                'user_id' => $id
            ]);

            if ($response->successful()) {
                $orderData = $response->json()['data'] ?? [];
                
                $result = $user->toArray();
                $result['orders'] = $orderData;

                return $this->successResponse("User and orders retrieved successfully", $result);
            } else {
                return $this->errorResponse("Failed to fetch data from OrderService", 502);
            }
        } catch (Exception $e) {
            return $this->errorResponse("OrderService unreachable", 502);
        }
    }
}
