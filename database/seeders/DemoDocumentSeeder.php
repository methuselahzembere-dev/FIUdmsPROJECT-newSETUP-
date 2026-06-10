<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DemoDocumentSeeder extends Seeder
{
    use FIUdmsSeederSupport;

    public function run(): void
    {
        if (! Schema::hasTable('documents') || ! Schema::hasTable('institutions') || ! Schema::hasTable('folders')) {
            $this->command?->warn('Skipping DemoDocumentSeeder because one or more required tables do not exist.');
            return;
        }

        $folderId = DB::table('folders')->where('slug', 'immediate-outcome-evidence')->value('id')
            ?? DB::table('folders')->value('id');

        $reviewerId = Schema::hasTable('users') ? DB::table('users')->where('role', 'fiu_reviewer')->value('id') : null;

        $documents = [
            ['short_name' => 'ZIMRA', 'title' => 'Risk-Based Compliance Submission Pack', 'status' => 'submitted', 'notes' => 'Initial submission for FIU review.'],
            ['short_name' => 'ZRP', 'title' => 'Investigation and Prosecution Effectiveness Evidence', 'status' => 'under-review', 'notes' => 'Evidence currently under FIU review.'],
            ['short_name' => 'JSC', 'title' => 'Judicial Orders and Confiscation Evidence Register', 'status' => 'changes-requested', 'notes' => 'FIU requested supporting documentation and metadata corrections.'],
        ];

        foreach ($documents as $document) {
            $institutionId = DB::table('institutions')->where('short_name', $document['short_name'])->value('id');

            if (! $institutionId || ! $folderId) {
                continue;
            }

            $payload = [
                'institution_id' => $institutionId,
                'folder_id' => $folderId,
                'title' => $document['title'],
                'file_path' => 'seeded/placeholders/' . strtolower($document['short_name']) . '-submission.pdf',
                'status' => $document['status'],
                'notes' => $document['notes'],
                'review_notes' => $document['status'] === 'changes-requested' ? 'Please attach the latest signed version and provide clearer indexing.' : null,
                'submitted_by' => Schema::hasTable('users') ? DB::table('users')->where('institution_id', $institutionId)->value('id') : null,
                'submitted_at' => now()->subDays(3),
                'reviewed_by' => $reviewerId,
                'reviewed_at' => in_array($document['status'], ['under-review', 'changes-requested'], true) ? now()->subDay() : null,
            ];

            DB::table('documents')->updateOrInsert(
                ['institution_id' => $institutionId, 'title' => $document['title']],
                $this->onlyExistingColumns('documents', array_merge($payload, $this->nowColumns('documents')))
            );
        }
    }
}
