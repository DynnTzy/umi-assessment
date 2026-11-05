<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Element;
use Illuminate\Database\Seeder;

class ElementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chapters = [
            'TKK' => [
                'TKK 1.1',
                'TKK 1.2',
                'TKK 1.3',
                'TKK 2.1',
                'TKK 2.2',
                'TKK 2.3',
                'TKK 3.1',
                'TKK 3.2',
                'TKK 3.3',
                'TKK 3.4',
                'TKK 3.5',
                'TKK 3.6',
                'TKK 3.7',
                'TKK 3.8',
                'TKK 3.9',
                'TKK 3.10',
                'TKK 4.1',
                'TKK 4.2',
                'TKK 4.3',
            ],

            'PMKP' => [
                'PMKP 1.1',
                'PMKP 1.2',
                'PMKP 1.3',
                'PMKP 1.4',
                'PMKP 1.5',
                'PMKP 2.1',
                'PMKP 2.2',
                'PMKP 2.3',
                'PMKP 2.4',
                'PMKP 2.5',
                'PMKP 2.6',
                'PMKP 2.7',
                'PMKP 2.8',
                'PMKP 3.1',
                'PMKP 3.2',
                'PMKP 3.3',
                'PMKP 3.4',
                'PMKP 3.5',
            ],

            'PKP' => [
                'PKP 1.1',
                'PKP 1.2',
                'PKP 1.3',
                'PKP 1.4',
                'PKP 1.5',
                'PKP 1.6',
                'PKP 1.7',
                'PKP 2.1',
                'PKP 2.2',
                'PKP 3.1',
                'PKP 3.2',
                'PKP 3.3',
                'PKP 3.4',
                'PKP 4.1',
                'PKP 4.2',
                'PKP 4.3',
                'PKP 5.1',
                'PKP 5.2',
                'PKP 5.3',
                'PKP 6.1',
                'PKP 6.2',
                'PKP 7.1',
                'PKP 7.2',
                'PKP 8.1',
                'PKP 8.2',
                'PKP 8.3',
                'PKP 8.4',
                'PKP 8.5',
                'PKP 8.6',
                'PKP 9.1',
                'PKP 9.2',
                'PKP 9.3',
                'PKP 9.4',
                'PKP 10.1',
                'PKP 10.2',
                'PKP 10.3',
                'PKP 11.1',
                'PKP 11.2',
                'PKP 11.3',
                'PKP 11.4',
                'PKP 11.5',
                'PKP 12.1',
                'PKP 12.2',
                'PKP 12.3',
                'PKP 12.4',
                'PKP 13.1',
                'PKP 13.2',
                'PKP 13.3',
                'PKP 13.4',
                'PKP 13.5',
                'PKP 13.6',
                'PKP 13.7',
                'PKP 14.1',
                'PKP 14.2',
                'PKP 15.1',
                'PKP 15.2',
                'PKP 15.3',
                'PKP 15.4',
                'PKP 15.5',
                'PKP 15.6',
                'PKP 15.7',
                'PKP 15.8',
                'PKP 15.9',
                'PKP 15.10',
                'PKP 15.11',
                'PKP 15.12',
                'PKP 15.13',
            ],
        ];

        foreach ($chapters as $chapterCode => $elementCodes) {
            $chapter = Chapter::where('code', $chapterCode)->firstOrFail();

            foreach ($elementCodes as $index => $elementCode) {
                Element::updateOrCreate(
                    [
                        'chapter_id' => $chapter->id,
                        'code' => $elementCode,
                    ],
                    [
                        'sort_order' => $index + 1,
                    ]
                );
            }
        }
    }
}
