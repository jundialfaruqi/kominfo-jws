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
            'phone' => '082172117001',
            'password' => Hash::make('admin123'),
            'address' => 'Pekanbaru',
            'status' => 'Active',
        ]);

        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@mail.com',
            'role' => 'Super Admin',
            'phone' => '082172117002',
            'password' => Hash::make('admin123'),
            'address' => 'Pekanbaru',
            'status' => 'Active',
        ]);

        $this->call([
            ProfilSeeder::class,
            JadwalSholatSeeder::class,
            AdzanSeeder::class,
            MarqueeSeeder::class,
        ]);

        // User::factory()->create([
        //     'name' => 'User',
        //     'email' => 'user@mail.com',
        //     'role' => 'User',
        //     'password' => Hash::make('admin123'),
        //     'address' => 'Pekanbaru',
        // ]);
    }
}
