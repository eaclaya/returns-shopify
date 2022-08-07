<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AccountSettingFactory extends Factory
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
            'account_id' => 1,
            'code' => config('services.shopify.code'),
        ];
    }
}
