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
            1 => [
                'title' => 'Immediate Outcome 1',
                'description' => 'Immediate Outcome 1',
                'subs' => [1, 2, 3, 4, 5, 6],
            ],
            2 => [
                'title' => 'Immediate Outcome 2',
                'description' => 'Immediate Outcome 2',
                'subs' => [1, 2, 3, 4],
            ],
            3 => [
                'title' => 'Immediate Outcome 3',
                'description' => 'Immediate Outcome 3',
                'subs' => [1, 2, 3, 4, 5, 6],
            ],
            4 => [
                'title' => 'Immediate Outcome 4',
                'description' => 'Immediate Outcome 4',
                'subs' => [1, 2, 3, 4, 5, 6],
            ],
            5 => [
                'title' => 'Immediate Outcome 5',
                'description' => 'Immediate Outcome 5',
                'subs' => [1, 2, 3, 4, 5],
            ],
            6 => [
                'title' => 'Immediate Outcome 6',
                'description' => 'Immediate Outcome 6',
                'subs' => [1, 2, 3, 4],
            ],
            7 => [
                'title' => 'Immediate Outcome 7',
                'description' => 'Immediate Outcome 7',
                'subs' => [1, 2, 3, 4],
            ],
            8 => [
                'title' => 'Immediate Outcome 8',
                'description' => 'Immediate Outcome 8',
                'subs' => [1, 2, 3, 4, 5, 6, 7],
            ],
            9 => [
                'title' => 'Immediate Outcome 9',
                'description' => 'Immediate Outcome 9',
                'subs' => [1, 2, 3, 4, 5],
            ],
            10 => [
                'title' => 'Immediate Outcome 10',
                'description' => 'Immediate Outcome 10',
                'subs' => [1, 2, 3, 4, 5],
            ],
            11 => [
                'title' => 'Immediate Outcome 11',
                'description' => 'Immediate Outcome 11',
                'subs' => [1, 2, 3, 4, 5, 6],
            ],
        ];

        foreach ($structure as $ioNumber => $io) {
            $immediateOutcome = EffectivenessImmediateOutcome::query()->updateOrCreate(
                ['code' => 'IO' . $ioNumber],
                [
                    'number' => $ioNumber,
                    'title' => $io['title'],
                    'description' => $io['description'],
                    'sort_order' => $ioNumber,
                    'is_active' => true,
                ]
            );

            foreach ($io['subs'] as $subNumber) {
                $code = $ioNumber . '.' . $subNumber;

                EffectivenessSubImmediateOutcome::query()->updateOrCreate(
                    ['code' => $code],
                    [
                        'immediate_outcome_id' => $immediateOutcome->id,
                        'main_number' => $ioNumber,
                        'sub_number' => $subNumber,
                        'title' => 'Sub-Immediate Outcome ' . $code,
                        'description' => 'Sub-Immediate Outcome ' . $code,
                        'sort_order' => $subNumber,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}

