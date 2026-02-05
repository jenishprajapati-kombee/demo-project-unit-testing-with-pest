<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory extends Factory
{
    protected $model = Country::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->text(95),
            'code' => $this->faker->text(95),
            'phone_code' => $this->faker->text(95),
            'currency' => $this->faker->text(95),
        ];
    }
}
