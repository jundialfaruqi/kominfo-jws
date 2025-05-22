<?php

namespace Database\Seeders;

use App\Models\Profil;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Profil::create([
            'name' => 'Masjid Al-Azhar',
            'address' => 'Jl. Raya Bogor',
            'phone' => '08123456789',
            'slug' => 'masjid-al-azhar',
            'user_id' => 1,
        ]);
    }
}
