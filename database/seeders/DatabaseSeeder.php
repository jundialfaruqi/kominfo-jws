<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(50)->create();
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
            'role' => 'Admin',
            'phone' => '081234567890',
            'password' => Hash::make('admin123'),
            'address' => 'Pekanbaru',
            'status' => 'Active',
        ]);

        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@mail.com',
            'role' => 'Super Admin',
            'phone' => '081234567890',
            'password' => Hash::make('admin123'),
            'address' => 'Pekanbaru',
            'status' => 'Active',
        ]);

        $this->call([
            RolePermissionSeeder::class,
            AssignRolesToUsersSeeder::class,
        ]);
    }
}
