<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => config('services.shopify.host'),
            'email' => $this->faker->unique()->safeEmail(),
            'is_active' => 1,
            'shop' => config('services.shopify.host'),
            'domain' => config('services.shopify.host'),
        ];
    }
}
