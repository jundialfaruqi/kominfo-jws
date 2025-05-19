<?php

namespace Database\Seeders;

use App\Models\Slides;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SlidesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Slides::factory(20)->create();
    }
}
