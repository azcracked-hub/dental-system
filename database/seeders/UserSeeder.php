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
        \App\Models\User::updateOrCreate(
        ['email' => 'admin@dentalclinic.com'],
        [
            'name'     => 'Dr. Mary Cris Estandarte',
            'password' => bcrypt('123456'),
            'role'     => 'admin',
            'phone'    => '+63 945 678 9012',
        ]
    );
    }
}
