<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(15)->create();
        $categories = Category::factory(5)->create();

        $products = Product::factory(40)
            ->recycle($categories)
            ->create();

        Cart::factory(10)
            ->recycle($users)
            ->hasCartItems(3, function () use ($products) {
                return [
                    'product_id' => $products->random()->id,
                    'quantity' => fake()->numberBetween(1, 5)
                ];
            })
            ->create();

        Order::factory(25)
            ->recycle($users)
            ->create();
    }
}
