<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->text(95),
            'description' => $this->faker->text(250),
            'code' => $this->faker->text(10),
            'price' => $this->faker->randomFloat(2, 1, 99999999),
        ];
    }
}
