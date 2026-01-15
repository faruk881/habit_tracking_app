<?php

namespace Database\Seeders;

use App\Models\ChallengeType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChallengeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
        'Health',
        'Fitness',
        'Productivity',
        'Learning',
    ];

        foreach ($types as $type) {
            ChallengeType::updateOrCreate(
                ['name' => $type]
            );
        }
    }
}
