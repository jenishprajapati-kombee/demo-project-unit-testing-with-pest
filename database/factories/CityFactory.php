<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
    protected $model = City::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'state_id' => \App\Models\State::inRandomOrder()->first()?->id,
            'name' => $this->faker->unique()->text(95),
        ];
    }
}
