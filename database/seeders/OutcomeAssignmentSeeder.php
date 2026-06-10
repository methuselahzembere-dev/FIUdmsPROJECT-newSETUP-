<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OutcomeAssignmentSeeder extends Seeder
{
    use FIUdmsSeederSupport;

    public function run(): void
    {
        if (! Schema::hasTable('institution_immediate_outcome') || ! Schema::hasTable('institutions') || ! Schema::hasTable('immediate_outcomes')) {
            $this->command?->warn('Skipping OutcomeAssignmentSeeder because one or more required tables do not exist.');
            return;
        }

        $assignments = [
            'ZIMRA' => [1, 3, 4, 5, 6],
            'ZRP' => [2, 6, 7, 8, 9, 10, 11],
            'JSC' => [2, 7, 8, 9, 11],
        ];

        foreach ($assignments as $shortName => $numbers) {
            $institutionId = DB::table('institutions')->where('short_name', $shortName)->value('id');

            if (! $institutionId) {
                continue;
            }

            foreach ($numbers as $number) {
                $outcomeId = DB::table('immediate_outcomes')->where('number', $number)->value('id');

                if (! $outcomeId) {
                    continue;
                }

                DB::table('institution_immediate_outcome')->updateOrInsert(
                    [
                        'institution_id' => $institutionId,
                        'immediate_outcome_id' => $outcomeId,
                    ],
                    $this->onlyExistingColumns('institution_immediate_outcome', array_merge([
                        'institution_id' => $institutionId,
                        'immediate_outcome_id' => $outcomeId,
                        'due_date' => now()->addDays(45)->toDateString(),
                        'assigned_at' => now(),
                        'assigned_by' => Schema::hasTable('users') ? DB::table('users')->where('role', 'fiu_admin')->value('id') : null,
                    ], $this->nowColumns('institution_immediate_outcome')))
                );
            }
        }
    }
}
