<?php

namespace Database\Seeders;

use App\Models\UserTag;
use Illuminate\Database\Seeder;

class UserTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        UserTag::factory()->count(5)->create();
    }
}
