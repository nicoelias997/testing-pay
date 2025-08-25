<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         Plan::create([
            'id'=> 1,
            'slug' => 'monthly',
            'price' => 5,
            'duration_in_days' => 30
        ]);

         Plan::create([
            'id' => 2,
            'slug' => 'yearly',
            'price' => 55,
            'duration_in_days' => 365
        ]);
    }
}
