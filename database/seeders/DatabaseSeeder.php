<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);

        if (app()->environment('local')) {
            for ($i = 1; $i < 50; $i++) {
                User::create([
                    'name' => 'Test ' . $i,
                    'email' => 'test' . $i . '@test.com',
                    'email_verified_at' => now(),
                    'password' => Hash::make('12345678'),
                    'access_admin' => 0,
                    'active' => fake()->boolean(),
                    'remember_token' => Str::random(10),
                ])->assignRole('user');
            }
        }

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
