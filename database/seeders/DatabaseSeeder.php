<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(EmailTemplateSeeder::class);
        $this->call(EmailFormatSeeder::class);
        $this->call(UserSeeder::class);

        $this->call(CountrySeeder::class);

        $this->call(StateSeeder::class);

        $this->call(CitySeeder::class);

        $this->call(WebUserSeeder::class);

        $this->call(UserTagSeeder::class);

        $this->call(BrandSeeder::class);

        $this->call(ProductSeeder::class);
    }
}
