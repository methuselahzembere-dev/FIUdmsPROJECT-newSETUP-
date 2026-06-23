<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Document;
use App\Models\Institution;
use App\Models\TechnicalComplianceFolder;

class DemoDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $documents = [
            ['short_name' => 'ZIMRA', 'title' => 'Risk-Based Compliance Submission Pack', 'status' => 'submitted', 'remarks' => 'Initial submission.'],
            ['short_name' => 'ZRP', 'title' => 'Investigation Evidence', 'status' => 'submitted', 'remarks' => 'Evidence for review.'],
        ];

        foreach ($documents as $docData) {
            $institution = Institution::where('short_name', $docData['short_name'])->first();
            $folder = TechnicalComplianceFolder::first(); // Assuming at least one folder exists

            if (!$institution) continue;

            // 1. Create the Document Node
            $document = Document::create([
                'workspace_track'       => 'technical',
                'visibility_scope'      => 'internal',
                'title'                 => $docData['title'],
                'reporting_institution' => $institution->name,
                'date_logged'           => now(),
                'status'                => $docData['status'],
                'remarks'               => $docData['remarks'],
                'file_path'             => 'seeded/placeholder.pdf',
                'user_id'               => 1, // Default admin user
            ]);

            // 2. Attach relationships via Pivot Tables
            $document->institutions()->attach($institution->id);
            $document->technicalFolders()->attach($folder->id);
        }
    }
}

