<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Exception;

class ProductController extends Controller
{
    use ApiResponse;

    /**
     * PROVIDER: GET /api/products/{id}
     */
    public function show($id): JsonResponse
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse("Invalid Product ID", 400);
        }

        $product = Product::find($id);

        if (!$product) {
            return $this->errorResponse("Product not found", 404);
        }

        return $this->successResponse("Product retrieved successfully", $product);
    }

    /**
     * CONSUMER: GET /api/products/{id}/owner
     * Memanggil UserService untuk mendapatkan data pemilik produk
     */
    public function showWithOwner($id): JsonResponse
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse("Invalid Product ID", 400);
        }

        $product = Product::find($id);
        if (!$product) {
            return $this->errorResponse("Product not found", 404);
        }

        try {
            // Mengambil base URL UserService dari config
            $baseUrl = config('services.user_service.base_url');
            $response = Http::timeout(5)->get("{$baseUrl}/api/users/{$product->user_id}");

            if ($response->successful()) {
                $userData = $response->json()['data'] ?? null;
                
                $result = $product->toArray();
                $result['owner'] = $userData;

                return $this->successResponse("Product and owner details retrieved successfully", $result);
            } else {
                return $this->errorResponse("Failed to fetch owner data from UserService", 502);
            }
        } catch (Exception $e) {
            return $this->errorResponse("UserService unreachable", 502);
        }
    }
}
