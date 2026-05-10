<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateProductStock implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $productId;
    protected $quantity;

    /**
     * Create a new job instance.
     */
    public function __construct(string $productId, int $quantity)
    {
        $this->productId = $productId;
        $this->quantity = $quantity;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Asynchronous Job: Updating stock for Product ID {$this->productId} (Quantity: {$this->quantity})");
        
        $product = Product::find($this->productId);
        
        if ($product) {
            $product->stock -= $this->quantity;
            $product->save();
            Log::info("Stock updated for Product ID {$this->productId}. New stock: {$product->stock}");
        } else {
            Log::error("Product ID {$this->productId} not found for stock update.");
        }
    }
}
