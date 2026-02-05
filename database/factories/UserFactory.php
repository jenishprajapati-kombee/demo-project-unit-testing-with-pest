<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->text(95),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('123456'),
            'role_id' => \App\Models\Role::inRandomOrder()->first()?->id,
            'dob' => $this->faker->date(),
            'profile' => $this->faker->text(95),
            'country_id' => \App\Models\Country::inRandomOrder()->first()?->id,
            'state_id' => \App\Models\State::inRandomOrder()->first()?->id,
            'city_id' => \App\Models\City::inRandomOrder()->first()?->id,
            'gender' => $this->faker->randomElement(['F', 'M']),
            'status' => $this->faker->randomElement(['Y', 'N']),
            'email_verified_at' => $this->faker->dateTime(),
            'remember_token' => $this->faker->text(95),
            'locale' => 'en',
        ];
    }
}
