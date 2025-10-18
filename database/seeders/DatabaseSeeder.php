<?php

namespace Database\Seeders;

use App\Models\Profil;
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
            'name' => 'Example Super Admin',
            'email' => 'superadmin@kominfo-jws.test',
            'role' => 'Super Admin',
            'phone' => '081234567890',
            'password' => Hash::make('terserah'),
            'address' => 'Pekanbaru',
            'status' => 'Active',
        ]);

        User::factory()->create([
            'name' => 'Example User',
            'email' => 'user@kominfo-jws.test',
            'role' => 'User',
            'phone' => '081234567890',
            'password' => Hash::make('terserah'),
            'address' => 'Pekanbaru',
            'status' => 'Active',
        ]);

        Profil::factory()->create([
            'name' => 'Masjid Programmer Pekanbaru',
            'address' => 'Jl. Jalan Healing Akhir Tahun',
            'phone' => '081234567890',
            'slug' => 'programmer-pku',
            'user_id' => 3,
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
