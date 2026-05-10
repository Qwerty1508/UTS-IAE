<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Jobs\ProcessOrderJob;

class OrderController extends Controller
{
    use ApiResponse;

    /**
     * PROVIDER: POST /api/orders
     * Membuat order baru setelah validasi ke User & Product Service
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string|uuid',
            'product_id' => 'required|string|uuid',
            'qty' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse("Invalid input: " . $validator->errors()->first(), 400);
        }

        $userId = $request->user_id;
        $productId = $request->product_id;
        $qty = $request->qty;

        // 1. CONSUMER: Validasi User exists ke UserService
        try {
            $userUrl = config('services.user_service.base_url');
            $userResponse = Http::timeout(10)->get("{$userUrl}/api/users/{$userId}");

            if (!$userResponse->successful()) {
                return $this->errorResponse("User not found", 404);
            }
        } catch (Exception $e) {
            return $this->errorResponse("UserService unreachable: " . $e->getMessage(), 502);
        }

        // 2. CONSUMER: Validasi Product & Stok ke ProductService
        try {
            $productUrl = config('services.product_service.base_url');
            $productResponse = Http::timeout(10)->get("{$productUrl}/api/products/{$productId}");

            if (!$productResponse->successful()) {
                return $this->errorResponse("Product not found", 404);
            }

            $productData = $productResponse->json()['data'];
            
            // Cek Stok
            if ($productData['stock'] < $qty) {
                return $this->errorResponse("Stok tidak mencukupi", 400);
            }

            $price = $productData['price'];
        } catch (Exception $e) {
            return $this->errorResponse("ProductService unreachable: " . $e->getMessage(), 502);
        }

        // 3. Simpan Order
        try {
            $totalPrice = $price * $qty;

            $order = Order::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'qty' => $qty,
                'total_price' => $totalPrice,
                'status' => 'pending'
            ]);

            // 4. Dispatch Asynchronous Job to RabbitMQ
            \App\Jobs\UpdateProductStock::dispatch($productId, $qty)->onQueue('product-stock-update');

            return $this->successResponse("Order created successfully", $order, 201);
        } catch (Exception $e) {
            return $this->errorResponse("Internal server error: " . $e->getMessage(), 500);
        }
    }

    /**
     * PROVIDER: GET /api/orders
     * Mengambil histori order berdasarkan user_id
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->query('user_id');

        $query = Order::query();

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $orders = $query->get();

        return $this->successResponse("Orders retrieved successfully", $orders);
    }

    /**
     * PROVIDER: GET /api/orders/{id}
     */
    public function show($id): JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return $this->errorResponse("Order not found", 404);
        }

        return $this->successResponse("Order retrieved successfully", $order);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $order = Order::find($id);
        if (!$order) return $this->errorResponse("Order not found", 404);
        $order->update($request->all());
        return $this->successResponse("Order updated successfully", $order);
    }

    public function destroy($id): JsonResponse
    {
        $order = Order::find($id);
        if (!$order) return $this->errorResponse("Order not found", 404);
        $order->delete();
        return $this->successResponse("Order deleted successfully");
    }
}
