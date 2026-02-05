<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

class BrandFactory extends Factory
{
    protected $model = Brand::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->text(95),
            'remark' => $this->faker->paragraph(),
            'bob' => $this->faker->dateTime(),
            'description' => $this->faker->text(250),
            'country_id' => \App\Models\Country::inRandomOrder()->first()?->id,
            'state_id' => \App\Models\State::inRandomOrder()->first()?->id,
            'city_id' => \App\Models\City::inRandomOrder()->first()?->id,
            'status' => $this->faker->randomElement(['Y', 'N']),
        ];
    }
}
