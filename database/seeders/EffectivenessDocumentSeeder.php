<?php

namespace Database\Seeders;

use App\Models\EffectivenessDocument;
use App\Models\EffectivenessSubImmediateOutcome;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class EffectivenessDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $subOutcomes = EffectivenessSubImmediateOutcome::query()
            ->with('immediateOutcome')
            ->active()
            ->orderBy('main_number')
            ->orderBy('sub_number')
            ->get();

        if ($subOutcomes->isEmpty()) {
            $this->command?->warn('No active Effectiveness sub-IO records found. Seed Immediate Outcomes and sub-IOs first.');

            return;
        }

        $statuses = ['logged', 'submitted', 'under_review', 'approved'];
        $institutions = [
            'Office of Planning and Quality Assurance',
            'College of Education',
            'Research, Innovation, and Extension Office',
            'Student Affairs and Services Office',
            'External Partnerships and Linkages Office',
        ];
        $titlePrefixes = [
            'Validation Report',
            'Monitoring Summary',
            'Accomplishment Narrative',
            'Evidence Portfolio',
            'Outcome Tracking Sheet',
            'Implementation Review',
        ];

        foreach ($subOutcomes as $index => $subOutcome) {
            $documentCount = ($index % 3) + 2;

            for ($i = 1; $i <= $documentCount; $i++) {
                $loggedAt = CarbonImmutable::now()->subDays(($index * 5) + ($i * 8));
                $status = $statuses[($index + $i - 1) % count($statuses)];
                $institution = $institutions[($index + $i - 1) % count($institutions)];
                $titlePrefix = $titlePrefixes[($index + $i - 1) % count($titlePrefixes)];
                $mainCode = $subOutcome->immediateOutcome?->code ?? 'IO';
                $title = sprintf('%s for %s (%s)', $titlePrefix, $subOutcome->code, $mainCode);
                $fileName = Str::slug($subOutcome->code.'-'.$titlePrefix.'-'.$i).'.pdf';

                EffectivenessDocument::query()->updateOrCreate(
                    [
                        'effectiveness_sub_io_id' => $subOutcome->id,
                        'title' => $title,
                    ],
                    [
                        'name' => $title,
                        'reporting_institution' => $institution,
                        'status' => $status,
                        'file_name' => $fileName,
                        'file_path' => 'effectiveness-documents/samples/'.$fileName,
                        'disk' => 'public',
                        'date_logged' => $loggedAt->toDateString(),
                        'document_date' => $loggedAt->subDays(5)->toDateString(),
                        'submitted_at' => in_array($status, ['submitted', 'under_review', 'approved'], true) ? $loggedAt->setTime(9, 30) : null,
                        'approved_at' => $status === 'approved' ? $loggedAt->addDays(3)->setTime(14, 0) : null,
                        'remarks' => sprintf(
                            'Sample seeded document for %s under %s. Use this record to validate split-dashboard browsing and counts.',
                            $subOutcome->code,
                            $mainCode
                        ),
                        'meta' => [
                            'seeded' => true,
                            'main_io_code' => $mainCode,
                            'sub_io_code' => $subOutcome->code,
                            'sample_rank' => $i,
                            'mime_type' => 'application/pdf',
                            'size_kb' => 256 + ($i * 32),
                            'tags' => Arr::wrap(['effectiveness', 'sample', Str::lower($status)]),
                        ],
                    ]
                );
            }
        }
    }
}