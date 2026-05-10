<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Processing Order #{$this->order->id} asynchronously.");

        // Simulate complex processing or cross-service sync
        try {
            $productUrl = config('services.product_service.base_url');
            
            // In a real scenario, we might call an endpoint to deduct stock
            $response = Http::post("{$productUrl}/api/products/{$this->order->product_id}/deduct-stock", [
                'qty' => $this->order->qty
            ]);

            if ($response->successful()) {
                $this->order->update(['status' => 'completed']);
                Log::info("Order #{$this->order->id} completed successfully.");
            } else {
                $this->order->update(['status' => 'failed']);
                Log::error("Failed to deduct stock for Order #{$this->order->id}.");
            }
        } catch (\Exception $e) {
            Log::error("Error processing Order #{$this->order->id}: " . $e->getMessage());
            $this->order->update(['status' => 'failed']);
        }
    }
}
