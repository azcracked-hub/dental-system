<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        Service::insert([
            [
                'name' => 'Dental Cleaning',
                'description' => 'Basic dental cleaning procedure',
                'price' => 2000,
                'duration_minutes' => 30
            ],
            [
                'name' => 'Tooth Extraction',
                'description' => 'Removal of damaged tooth',
                'price' => 800,
                'duration_minutes' => 45
            ],
            [
                'name' => 'Restoration',
                'description' => 'Tooth restoration filling',
                'price' => 1500,
                'duration_minutes' => 30
            ],
            [
                'name' => 'Crown Jacket',
                'description' => 'Dental crown installation',
                'price' => 4000,
                'duration_minutes' => 60
            ],
            [
                'name' => 'Teeth Whitening',
                'description' => 'Whitening treatment',
                'price' => 10000,
                'duration_minutes' => 45
            ],
            [
                'name' => 'Braces Adjustment',
                'description' => 'Adjustment of braces',
                'price' => 1000,
                'duration_minutes' => 30
            ],
            [
                'name' => 'Braces Installation',
                'description' => 'Full braces installation',
                'price' => 25000,
                'duration_minutes' => 90
            ],
            [
                'name' => 'Denture',
                'description' => 'Denture fitting',
                'price' => 1500,
                'duration_minutes' => 60
            ],
        ]);
    }
}
