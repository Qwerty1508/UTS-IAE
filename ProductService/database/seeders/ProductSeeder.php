<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Laptop ASUS ROG',
                'price' => 15000000,
                'stock' => 10,
                'user_id' => '019e12bc-eaa5-7320-ba8c-1e280f187f97',
            ],
            [
                'name' => 'Smartphone Samsung S23',
                'price' => 12000000,
                'stock' => 20,
                'user_id' => '019e12bc-eaa5-7320-ba8c-1e280f187f97',
            ],
            [
                'name' => 'Monitor Dell UltraSharp',
                'price' => 5000000,
                'stock' => 15,
                'user_id' => '019e12bc-eaa5-7320-ba8c-1e280f187f97',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
