<?php

namespace Database\Seeders;

use App\Models\Owner;
use App\Models\Patient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owners = Owner::all();

        if ($owners->count() === 0) {
            $this->command->info('No owners found. Please seed the owners table first.');
            return;
        }

        foreach (range(1, 20) as $i) {
            Patient::create([
                'name' => fake()->name(),
                'date_of_birth' => fake()->date('Y-m-d', now()),
                'owner_id' => $owners->random()->id,
                'type' => fake()->randomElement(['Dog', 'Cat', 'Bird', 'Reptile']),
            ]);
        }
    }
}
