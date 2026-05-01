<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ADMIN
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@dentalclinic.com'],
            [
                'name'     => 'Dr. Mary Cris Estandarte',
                'password' => bcrypt('123456'),
                'role'     => 'admin',
                'phone'    => '+63 945 678 9012',
            ]
        );

        // STAFF
        \App\Models\User::updateOrCreate(
            ['email' => 'staff@dentalclinic.com'],
            [
                'name'     => 'Clinic Staff',
                'password' => bcrypt('123456'),
                'role'     => 'staff',
                'phone'    => '+63 912 345 6789',
            ]
        );

        // PATIENT
        \App\Models\User::updateOrCreate(
            ['email' => 'patient@dentalclinic.com'],
            [
                'name'     => 'Juan Dela Cruz',
                'password' => bcrypt('123456'),
                'role'     => 'patient',
                'phone'    => '+63 923 456 7890',
            ]
        );
    }
}
