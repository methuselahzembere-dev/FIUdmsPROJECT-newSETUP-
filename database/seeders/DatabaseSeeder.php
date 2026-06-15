<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            InstitutionSeeder::class,
            ComplianceFrameworkSeeder::class, 
            TechnicalComplianceFolderSeeder::class, 
            UserSeeder::class,
            OutcomeAssignmentSeeder::class,
            DemoDocumentSeeder::class,
            EffectivenessImmediateOutcomesSeeder::class,
            TechnicalComplianceDocumentSeeder::class,
            EffectivenessDocumentSeeder::class,
        ]);
    }
}