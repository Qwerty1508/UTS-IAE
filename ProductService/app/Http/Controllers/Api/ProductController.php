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
            'id' => 'required|string|uuid',
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

    public function index(): JsonResponse
    {
        return $this->successResponse("Products retrieved successfully", Product::all());
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'price' => 'required|integer',
            'stock' => 'required|integer',
            'user_id' => 'required|uuid',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse("Validation failed", 400, $validator->errors());
        }

        $product = Product::create($request->all());
        return $this->successResponse("Product created successfully", $product, 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) return $this->errorResponse("Product not found", 404);
        $product->update($request->all());
        return $this->successResponse("Product updated successfully", $product);
    }

    public function destroy($id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) return $this->errorResponse("Product not found", 404);
        $product->delete();
        return $this->successResponse("Product deleted successfully");
    }

    /**
     * CONSUMER: GET /api/products/{id}/owner
     * Memanggil UserService untuk mendapatkan data pemilik produk
     */
    public function showWithOwner($id): JsonResponse
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|string|uuid',
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

    /**
     * PROVIDER: POST /api/products/{id}/update-stock
     * Sinkron/Blocking update stock (for performance comparison)
     */
    public function updateStock(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse("Invalid input", 400);
        }

        $product = Product::find($id);
        if (!$product) {
            return $this->errorResponse("Product not found", 404);
        }

        // Simulating slow operation
        sleep(5);

        $product->stock -= $request->product_quantity;
        $product->save();

        return $this->successResponse("Stock updated successfully (Synchronous)", $product);
    }
}
