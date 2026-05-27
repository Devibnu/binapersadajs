<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            UserSeeder::class,
            HeroBannerSeeder::class,
            ServiceSeeder::class,
            ProjectCategorySeeder::class,
            BlogSeeder::class,
            ContactPageSettingSeeder::class,
            HomepageSettingSeeder::class,
            HomepageVideoSeeder::class,
            AboutPageSettingSeeder::class,
            RolePermissionSeeder::class,
        ]);
    }
}
