<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Ingredient;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\Log;

class StockService
{
    private const LOW_STOCK_THRESHOLD = 50;

    public function updateStock(Collection $products): void
    {
        foreach ($products as $product) {
            foreach ($product->ingredients as $ingredient) {
                $consumed = $ingredient->pivot->quantity * $product->pivot->quantity; 
               
                Log::info("Consuming {$consumed} grams of {$ingredient->name}.");
    
                $ingredient->stock -= $consumed;
                $ingredient->save();
    
                if ($this->hasReachedLowStockThreshold($ingredient)) {
                    $this->notifyLowStock($ingredient);
                }
            }
        }
    }

    private function notifyLowStock(Ingredient $ingredient): void
    {
        if (!$ingredient->low_stock) {
            $ingredient->notify(new LowStockNotification());
            $ingredient->low_stock = true;
            $ingredient->save();
        }
    }

    private function hasReachedLowStockThreshold(Ingredient $ingredient): bool
    {
        return $ingredient->stock <= ($ingredient->initial_stock * (SELF::LOW_STOCK_THRESHOLD / 100));
    }
}
