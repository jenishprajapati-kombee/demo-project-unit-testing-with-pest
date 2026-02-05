<?php

namespace Database\Seeders;

use App\Models\WebUser;
use Illuminate\Database\Seeder;

class WebUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        WebUser::factory()->count(5)->create();
    }
}
