<?php

namespace Database\Seeders;

use App\Models\Label;
use Illuminate\Database\Seeder;

class LabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $labels = [
            'Personal',
            'Work',
            'Family',
            'Friends',
            'Home',
            'Health',
            'Shopping',
            'Travel',
            'Fitness',
            'Charity',
        ];

        foreach ($labels as $label) {
            Label::create([
                'name' => $label,
            ]);
        }
    }
}
