<?php
namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function processOrder(array $orderData): Order
    {
        return DB::transaction(function () use ($orderData) {
            $order = Order::create();
            foreach ($orderData['products'] as $product) {
                $order->products()->attach($product['product_id'], ['quantity' => $product['quantity']]);
            }
            $this->stockService->updateStock($order->products);

            return $order;
        });
    }
}
