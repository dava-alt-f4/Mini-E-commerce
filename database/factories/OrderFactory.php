<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => 'INV-'.date('Ymd').'-'.fake()->unique()->numerify('####'),
            'user_id' => User::factory(),
            'total_price' => 0,
            'status' => fake()->randomElement(['pending', 'paid', 'shipped', 'completed', 'cancelled']),
        ];
    }

    public function configure(): Factory
    {
        return $this->afterCreating(function (Order $order) {
            $products = Product::inRandomOrder()->take(fake()->numberBetween(1, 4))->get();

            $totalPrice = 0;

            foreach ($products as $product) {
                $quantity = fake()->numberBetween(1, 3);

                $order->orderItems()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);

                $totalPrice += ($product->price * $quantity);
            }

            $order->update(['total_price' => $totalPrice]);
        });
    }
}
