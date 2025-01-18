<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Ingredient;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\Log;

class StockService
{
    private const LOW_STOCK_THRESHOLD_PERCENT = 50; // we can make this as a config value

    public function updateStock(Collection $products): void
    {
        $products->each(function ($product) {
            $product->ingredients->each(function ($ingredient) use ($product) {
                $this->processIngredientConsumption($ingredient, $product->pivot->quantity);
            });
        });
    }

    /**
     * Process the stock consumption for a single ingredient.
     *
     * @param Ingredient $ingredient
     * @param int $productQuantity
     * @return void
     */
    private function processIngredientConsumption(Ingredient $ingredient, int $productQuantity): void
    {
        // to avoid using flags, we can calculate the low stock threshold
        $currentStock = $ingredient->stock;
        $lowStockThreshold = $ingredient->initial_stock * (self::LOW_STOCK_THRESHOLD_PERCENT / 100);
        $consumedQuantity = $ingredient->pivot->quantity * $productQuantity;

        Log::info("Consuming {$consumedQuantity} grams of {$ingredient->name}.");

        $ingredient->decrement('stock', $consumedQuantity);

        $newStock = $ingredient->stock;

        if ($currentStock >= $lowStockThreshold && $newStock < $lowStockThreshold) {
            $this->sendLowStockNotification($ingredient);
        }
    }

    private function sendLowStockNotification(Ingredient $ingredient): void
    {
        Log::info("sendLowStockNotification Processed  {$ingredient->name}.");

        $ingredient->notify(new LowStockNotification());
    }
}
