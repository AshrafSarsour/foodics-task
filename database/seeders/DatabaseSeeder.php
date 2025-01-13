<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $beef = Ingredient::create(['name' => 'Beef', 'stock' => 20000,'initial_stock'=>20000]); // 20kg = 20000 grams
        $cheese = Ingredient::create(['name' => 'Cheese', 'stock' => 5000,'initial_stock'=>5000]); // 5kg = 5000 grams
        $onion = Ingredient::create(['name' => 'Onion', 'stock' => 1000,'initial_stock'=>1000]); // 1kg = 1000 grams

        $burger = Product::create(['name' => 'Burger']);
        $burger->ingredients()->attach([
            $beef->id => ['quantity' => 150], // 150 grams
            $cheese->id => ['quantity' => 30], // 30 grams
            $onion->id => ['quantity' => 20], // 20 grams
        ]);
    }
}
