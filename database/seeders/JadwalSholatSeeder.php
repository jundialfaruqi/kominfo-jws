<?php

namespace Database\Seeders;

use App\Models\JadwalSholat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JadwalSholatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JadwalSholat::create([
            'user_id' => 1,
            'nama_jadwal' => 'Jadwal Sholat',
            'subuh' => '05:00',
            'dzuhur' => '04:30',
            'ashar' => '12:00',
            'maghrib' => '15:00',
            'isya' => '17:00',
            'tanggal' => '01',
            'bulan' => '01',
            'tahun' => '2023',
            'azan' => '4',
            'iqomah' => '6',
        ]);
    }
}
