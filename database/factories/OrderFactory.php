<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
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
            'user_id' => User::factory(),
            'order_number' => strtoupper(fake()->bothify('ORD-#####')),
            'status' => fake()->randomElement(OrderStatus::cases()),
            'payment_status' => fake()->randomElement(PaymentStatus::cases()),
            'payment_method' => fake()->randomElement(['card', 'transfer']),
            'subtotal' => 0,
            'tax' => 0,
            'shipping_cost' => 0,
            'total' => 0,
            'placed_at' => fake()->boolean(70) ? now()->subDays(rand(1, 10)) : null,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
