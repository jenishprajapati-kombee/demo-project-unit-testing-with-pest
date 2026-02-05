<?php

namespace Database\Factories;

use App\Models\UserTag;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserTagFactory extends Factory
{
    protected $model = UserTag::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => \App\Models\User::inRandomOrder()->first()?->id,
            'keyword' => $this->faker->text(95),
        ];
    }
}
