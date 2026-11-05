<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => 'password',
                'role' => 'admin',
                'clinic_id' => null,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'asesor@example.com'],
            [
                'name' => 'Asesor',
                'password' => 'password',
                'role' => 'asesor',
                'clinic_id' => null,
                'email_verified_at' => now(),
            ]
        );

        $clinic = Clinic::where('code', 'CMG')->firstOrFail();

        User::updateOrCreate(
            ['email' => 'klinik@example.com'],
            [
                'name' => 'Cipta Medika',
                'password' => 'password',
                'role' => 'klinik',
                'clinic_id' => $clinic->id,
                'email_verified_at' => now(),
            ]
        );
    }
}
