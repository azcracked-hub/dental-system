<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@dentalclinic.com'],
            [
                'name'     => 'Dr. Mary Cris Estandarte',
                'password' => bcrypt('Password1'),
                'role'     => 'admin',
                'phone'    => '+63 945 678 9012',
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@dentalclinic.com'],
            [
                'name'     => 'Clinic Staff',
                'password' => bcrypt('Password1'),
                'role'     => 'staff',
                'phone'    => '+63 912 345 6789',
            ]
        );

        $patientUser = User::updateOrCreate(
            ['email' => 'kennethagato08@gmail.com'],
            [
                'name'     => 'Kenneth Agato',
                'password' => bcrypt('Kenneth123'),
                'role'     => 'patient',
                'phone'    => '+63 923 456 7890',
            ]
        );

        Patient::updateOrCreate(
            ['email' => $patientUser->email],
            [
                'user_id' => $patientUser->id,
                'name'    => $patientUser->name,
                'phone'   => $patientUser->phone,
            ]
        );
    }
}
