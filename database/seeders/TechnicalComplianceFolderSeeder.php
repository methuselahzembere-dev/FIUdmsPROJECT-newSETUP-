<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TechnicalComplianceFolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Fetch the master Technical Compliance track ID dynamically
        $trackId = DB::table('compliance_tracks')
            ->where('slug', 'technical-compliance')
            ->value('id');

        if (!$trackId) {
            $this->command->error('Compliance track "technical-compliance" not found. Please run ComplianceFrameworkSeeder first.');
            return;
        }

        // 2. Define the core, mandatory technical compliance folders
        $defaultFolders = [
            [
                'name' => 'Acts and Statutory Instruments',
                'description' => 'Primary anti-money laundering legislation, acts, and subsidiary statutory instrument updates.',
            ],
            [
                'name' => 'Case Studies',
                'description' => 'Typologies, compliance assessment case files, and empirical financial crime trends.',
            ],
            [
                'name' => 'Enforcement Means',
                'description' => 'Directives, intervention records, administrative sanctions, and regulatory penalties criteria.',
            ],
            [
                'name' => 'Recommendations',
                'description' => 'FATF international compliance evaluation parameters and strategic recommendation frameworks.',
            ],
            [
                'name' => 'Regulations',
                'description' => 'Active operational guidelines and legal compliance regulations issued to external reporting entities.',
            ],
            [
                'name' => 'Risk Assessments and Strategies',
                'description' => 'National risk assessments, sector vulnerability analysis, and strategic risk mitigation plans.',
            ],
        ];

        // 3. Seed folders safely using updateOrInsert to prevent duplicate key constraint errors
        foreach ($defaultFolders as $folder) {
            $slug = Str::slug($folder['name']);

            DB::table('folders')->updateOrInsert(
                [
                    'compliance_track_id' => $trackId,
                    'slug' => $slug,
                    'parent_id' => null, // Root workspace level
                ],
                [
                    'name' => $folder['name'],
                    'description' => $folder['description'],
                    'institution_id' => null, // Global systemic defaults visible to everyone
                    'is_default' => true,
                    'is_visible_to_institutions' => true,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('Technical Compliance default workspace folders seeded successfully.');
    }
}