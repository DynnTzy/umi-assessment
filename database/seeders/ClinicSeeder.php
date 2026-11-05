<?php

namespace Database\Seeders;

use App\Models\Clinic;
use Illuminate\Database\Seeder;

class ClinicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Clinic::create([
            'code' => 'CMG',
            'name' => 'Cipta Medika',
            'address' => 'Gresik',
            'phone' => null,
            'email' => null,
        ]);
    }
}
