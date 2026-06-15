<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TechnicalComplianceFolder;
use App\Models\TechnicalComplianceDocument;
use App\Models\User;
use App\Models\Institution;

class TechnicalComplianceDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Establish dependencies or fall back to mock profiles if the database is fresh
        $user = User::first() ?: User::factory()->create(['name' => 'Compliance Officer']);
        $institution = Institution::first() ?: Institution::factory()->create(['name' => 'Commercial Bank of Zimbabwe']);

        // 2. Grab ALL folders belonging to the Technical Compliance track (ID 1)
        $folders = TechnicalComplianceFolder::where('compliance_track_id', 1)->get();

        if ($folders->isEmpty()) {
            $this->command->warn('No Technical Compliance folders found. Please seed folders first.');
            return;
        }

        // 3. 🌟 LOOP THROUGH EVERY FOLDER and seed documents inside them!
        foreach ($folders as $index => $folder) {
            
            // Document A: A standard submitted document
            TechnicalComplianceDocument::create([
                'folder_id'         => $folder->id,
                'institution_id'    => $institution->id,
                'uploaded_by'       => $user->id,
                'updated_by'        => null, 
                'title'             => str_replace(' ', '_', $folder->name) . '_Compliance_Report_Q2.pdf',
                'description'       => "Quarterly review data specific to the {$folder->name} node infrastructure.",
                'stored_path'       => "compliance/docs/folder_{$folder->id}_report.pdf",
                'original_filename' => 'report.pdf',
                'mime_type'         => 'application/pdf',
                'status'            => 'submitted',
                'submitted_at'      => now()->subDays($index),
            ]);

            // Document B: A verified audit trail spreadsheet (only for even-numbered folders to keep it varied!)
            if ($index % 2 === 0) {
                TechnicalComplianceDocument::create([
                    'folder_id'         => $folder->id,
                    'institution_id'    => $institution->id,
                    'uploaded_by'       => $user->id,
                    'updated_by'        => $user->id, 
                    'title'             => str_replace(' ', '_', $folder->name) . '_Audit_Matrix.csv',
                    'description'       => "Verified operational framework checklists for {$folder->name}.",
                    'stored_path'       => "compliance/docs/folder_{$folder->id}_matrix.csv",
                    'original_filename' => 'matrix.csv',
                    'mime_type'         => 'text/csv',
                    'status'            => 'verified',
                    'submitted_at'      => now()->subDays($index + 2),
                    'reviewed_at'       => now()->subDays(1),
                    'reviewed_by'       => $user->id,
                ]);
            }
        }

        $this->command->info('Successfully seeded mock documents into ALL technical compliance folders!');
    }
}