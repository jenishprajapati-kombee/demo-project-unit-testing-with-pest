<?php

namespace Database\Factories;

use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

class StateFactory extends Factory
{
    protected $model = State::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'country_id' => \App\Models\Country::inRandomOrder()->first()?->id,
            'name' => $this->faker->unique()->text(95),
        ];
    }
}
