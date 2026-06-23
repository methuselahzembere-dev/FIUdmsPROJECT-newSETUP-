<?php

namespace Database\Seeders;

use App\Models\EffectivenessImmediateOutcome;
use App\Models\EffectivenessSubImmediateOutcome;
use Illuminate\Database\Seeder;

class EffectivenessImmediateOutcomesSeeder extends Seeder
{
    public function run(): void
    {
        $structure = [
            1 => ['name' => 'Immediate Outcome 1', 'description' => 'Immediate Outcome 1', 'subs' => [1, 2, 3, 4, 5, 6]],
            2 => ['name' => 'Immediate Outcome 2', 'description' => 'Immediate Outcome 2', 'subs' => [1, 2, 3, 4]],
            3 => ['name' => 'Immediate Outcome 3', 'description' => 'Immediate Outcome 3', 'subs' => [1, 2, 3, 4, 5, 6]],
            4 => ['name' => 'Immediate Outcome 4', 'description' => 'Immediate Outcome 4', 'subs' => [1, 2, 3, 4, 5, 6]],
            5 => ['name' => 'Immediate Outcome 5', 'description' => 'Immediate Outcome 5', 'subs' => [1, 2, 3, 4, 5]],
            6 => ['name' => 'Immediate Outcome 6', 'description' => 'Immediate Outcome 6', 'subs' => [1, 2, 3, 4]],
            7 => ['name' => 'Immediate Outcome 7', 'description' => 'Immediate Outcome 7', 'subs' => [1, 2, 3, 4]],
            8 => ['name' => 'Immediate Outcome 8', 'description' => 'Immediate Outcome 8', 'subs' => [1, 2, 3, 4, 5, 6, 7]],
            9 => ['name' => 'Immediate Outcome 9', 'description' => 'Immediate Outcome 9', 'subs' => [1, 2, 3, 4, 5]],
            10 => ['name' => 'Immediate Outcome 10', 'description' => 'Immediate Outcome 10', 'subs' => [1, 2, 3, 4, 5]],
            11 => ['name' => 'Immediate Outcome 11', 'description' => 'Immediate Outcome 11', 'subs' => [1, 2, 3, 4, 5, 6]],
        ];

        foreach ($structure as $ioNumber => $io) {
            // Create or update the Main Immediate Outcome
            $immediateOutcome = EffectivenessImmediateOutcome::query()->updateOrCreate(
                ['number' => $ioNumber], // Unique lookup key
                [
                    'code'        => 'IO' . $ioNumber,
                    'name'        => $io['name'],
                    'description' => $io['description'],
                    'sort_order'  => $ioNumber,
                    'is_active'   => true,
                ]
            );

            // Create or update the Sub-Immediate Outcomes
            foreach ($io['subs'] as $subNumber) {
                $code = $ioNumber . '.' . $subNumber;

                EffectivenessSubImmediateOutcome::query()->updateOrCreate(
                    [
                        'main_number' => $ioNumber, 
                        'sub_number'  => $subNumber
                    ],
                    [
                        'code'                 => $code,
                        'immediate_outcome_id' => $immediateOutcome->id,
                        'name'                 => 'Sub-Immediate Outcome ' . $code,
                        'description'          => 'Sub-Immediate Outcome ' . $code,
                        'sort_order'           => $subNumber,
                        'is_active'            => true,
                    ]
                );
            }
        }
    }
}