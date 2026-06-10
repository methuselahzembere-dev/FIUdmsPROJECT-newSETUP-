<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            InstitutionSeeder::class,
            ComplianceFrameworkSeeder::class, // Generates the core compliance tracks
            TechnicalComplianceFolderSeeder::class, // 🌟 Added here: Seeds default track folders
            UserSeeder::class,
            OutcomeAssignmentSeeder::class,
            DemoDocumentSeeder::class,
            EffectivenessImmediateOutcomesSeeder::class,
        ]);
    }
}