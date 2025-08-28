<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'Mazlan',
            'email' => 'mazlan@kominfo-jws.pekanbaru.go.id',
            'role' => 'Super Admin',
            'phone' => '082287530693',
            'address' => 'Pekanbaru',
            'password' => bcrypt('terserah'),
            'status' => 'Active',
        ]);
    }
}
