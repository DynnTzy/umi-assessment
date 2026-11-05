<?php

namespace Database\Seeders;

use App\Models\Chapter;
use Illuminate\Database\Seeder;

class ChapterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Chapter::updateOrCreate(
            ['code' => 'TKK'],
            [
                'name' => 'Tata Kelola Klinik',
                'sort_order' => 1,
            ]
        );

        Chapter::updateOrCreate(
            ['code' => 'PMKP'],
            [
                'name' => 'Peningkatan Mutu dan Keselamatan Pasien',
                'sort_order' => 2,
            ]
        );

        Chapter::updateOrCreate(
            ['code' => 'PKP'],
            [
                'name' => 'Penyelenggaraan Kesehatan Perseorangan',
                'sort_order' => 3,
            ]
        );
    }
}
