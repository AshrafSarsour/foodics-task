<?php

namespace Tests\Unit;

use App\Models\Ingredient;
use App\Models\Product;
use App\Notifications\LowStockNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use DatabaseTransactions;
    // This trait will rollback all database changes after each test
    // its better to do this to avoid any side effects between tests

    public function test_order_updates_ingredient_stock()
    {
        $beef = Ingredient::create([
            'name' => 'Beef-Test',
            'stock' => 20000,
            'initial_stock' => 20000,
        ]);

        $burger = Product::create([
            'name' => 'Burger-Test',
            'price' => 5.99,
        ]);

        $burger->ingredients()->attach($beef->id, ['quantity' => 150]); // 150 grams per burger

        $initialBeefStock = $beef->stock;

        $response = $this->postJson('/api/orders', [
            'products' => [
                ['product_id' => $burger->id, 'quantity' => 3], // 3 Burgers
            ],
        ]);
        $responseData = $response->json();
        $response->assertStatus(200);

        $expectedBeefStock = $initialBeefStock - 150 * 3; // 150g x 3 burgers

        $this->assertDatabaseHas('ingredients', [
            'name' => 'Beef-Test',
            'stock' => $expectedBeefStock,
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $responseData['order']['id'],
        ]);
    }

    public function test_low_stock_notification()
    {
        Notification::fake();
    
        $beef = Ingredient::create([
            'name' => 'Beef-LowStock',
            'stock' => 20000,
            'initial_stock' => 20000,
        ]);
    
        $burger = Product::create([
            'name' => 'Burger-LowStock',
            'price' => 5.99,
        ]);
    
        $burger->ingredients()->attach($beef->id, ['quantity' => 150]); // 150 grams per burger
    
        $response = $this->postJson('/api/orders', [
            'products' => [
                ['product_id' => $burger->id, 'quantity' => 130], // 130 Burgers
            ],
        ]);
    
        $response->assertStatus(200);
    
        $beef->refresh();
        $expectedStock = 20000 - (150 * 130); 
    
        $this->assertTrue($expectedStock < ($beef->initial_stock / 2));
        $this->assertTrue(20000 >= ($beef->initial_stock / 2));
    
        $this->assertDatabaseHas('ingredients', [
            'name' => 'Beef-LowStock',
            'stock' => $expectedStock,
        ]);
    
        Notification::assertSentTo($beef, LowStockNotification::class);
    }
    
    public function test_low_stock_notification_sent_only_once()
    {
        Notification::fake();

        $beef = Ingredient::create(['name' => 'Beef', 'stock' => 20000, 'initial_stock' => 20000]);

        $burger = Product::create(['name' => 'Burger', 'price' => 5.99]);
        $burger->ingredients()->attach($beef->id, ['quantity' => 150]);

        $this->postJson('/api/orders', [
            'products' => [['product_id' => $burger->id, 'quantity' => 67]],
        ]);

        $this->postJson('/api/orders', [
            'products' => [['product_id' => $burger->id, 'quantity' => 10]],
        ]);

        Notification::assertSentToTimes($beef, LowStockNotification::class, 1);
    }

}
