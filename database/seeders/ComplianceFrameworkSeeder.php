<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ComplianceFrameworkSeeder extends Seeder
{
    use FIUdmsSeederSupport;

    public function run(): void
    {
        $this->seedComplianceTracks();
        $this->seedFolders();
        $this->seedImmediateOutcomes();
    }

    protected function seedComplianceTracks(): void
    {
        if (! Schema::hasTable('compliance_tracks')) {
            $this->command?->warn('Skipping compliance tracks because the compliance_tracks table does not exist.');
            return;
        }

        $tracks = [
            ['name' => 'Technical Compliance', 'description' => 'Document track for legal, institutional, supervisory, preventive, and operational technical compliance evidence.'],
            ['name' => 'Effectiveness', 'description' => 'Document track aligned to the standard 11 Immediate Outcomes framework.'],
        ];

        foreach ($tracks as $track) {
            $payload = array_merge($track, ['slug' => Str::slug($track['name'])], $this->nowColumns('compliance_tracks'));

            DB::table('compliance_tracks')->updateOrInsert(
                ['name' => $track['name']],
                $this->onlyExistingColumns('compliance_tracks', $payload)
            );
        }
    }

    protected function seedFolders(): void
    {
        if (! Schema::hasTable('folders') || ! Schema::hasTable('compliance_tracks')) {
            $this->command?->warn('Skipping folders because the folders or compliance_tracks table does not exist.');
            return;
        }

        $technicalTrackId = DB::table('compliance_tracks')->where('slug', 'technical-compliance')->value('id');
        $effectivenessTrackId = DB::table('compliance_tracks')->where('slug', 'effectiveness')->value('id');

        $folders = [
            ['track_id' => $technicalTrackId, 'name' => 'Legal and Institutional Framework', 'description' => 'Core legislative, policy, and institutional framework evidence.'],
            ['track_id' => $technicalTrackId, 'name' => 'Preventive Measures', 'description' => 'CDD, record keeping, reporting, controls, and supervision evidence.'],
            ['track_id' => $technicalTrackId, 'name' => 'Supervision and Sanctions', 'description' => 'Risk-based supervision, enforcement actions, sanctions, and remediation evidence.'],
            ['track_id' => $effectivenessTrackId, 'name' => 'Immediate Outcome Evidence', 'description' => 'Evidence submissions linked to assigned Immediate Outcomes.'],
            ['track_id' => $effectivenessTrackId, 'name' => 'Effectiveness Reviews', 'description' => 'FIU review notes, rating analysis, and effectiveness evaluation material.'],
        ];

        foreach ($folders as $folder) {
            if (! $folder['track_id']) {
                continue;
            }

            $payload = [
                'compliance_track_id' => $folder['track_id'],
                'name' => $folder['name'],
                'slug' => Str::slug($folder['name']),
                'description' => $folder['description'],
                'is_visible_to_institutions' => true,
            ];

            DB::table('folders')->updateOrInsert(
                ['name' => $folder['name']],
                $this->onlyExistingColumns('folders', array_merge($payload, $this->nowColumns('folders')))
            );
        }
    }

    protected function seedImmediateOutcomes(): void
    {
        if (! Schema::hasTable('immediate_outcomes')) {
            $this->command?->warn('Skipping Immediate Outcomes because the immediate_outcomes table does not exist.');
            return;
        }

        $outcomes = [
            1 => ['Risk, Policy and Coordination', 'Money laundering and terrorist financing risks are understood and coordinated actions mitigate the risks.'],
            2 => ['International Cooperation', 'International cooperation delivers appropriate information, financial intelligence, evidence, and action against criminals and assets.'],
            3 => ['Supervision', 'Supervisors appropriately supervise, monitor, and regulate financial institutions and DNFBPs for compliance.'],
            4 => ['Preventive Measures', 'Financial institutions and DNFBPs adequately apply preventive measures and report suspicious transactions.'],
            5 => ['Legal Persons and Arrangements', 'Legal persons and arrangements are prevented from misuse for money laundering or terrorist financing.'],
            6 => ['Financial Intelligence', 'Financial intelligence and relevant information are appropriately used by competent authorities.'],
            7 => ['Money Laundering Investigation and Prosecution', 'Money laundering offences and activities are investigated and offenders are prosecuted and sanctioned.'],
            8 => ['Confiscation', 'Proceeds and instrumentalities of crime are confiscated.'],
            9 => ['Terrorist Financing Investigation and Prosecution', 'Terrorist financing offences and activities are investigated and sanctioned.'],
            10 => ['Terrorist Financing Preventive Measures and Sanctions', 'Terrorists and terrorist organisations are prevented from raising, moving, and using funds.'],
            11 => ['Proliferation Financing Sanctions', 'Persons and entities involved in proliferation of weapons of mass destruction are prevented from raising, moving, and using funds.'],
        ];

        foreach ($outcomes as $number => [$title, $description]) {
            DB::table('immediate_outcomes')->updateOrInsert(
                ['number' => $number],
                $this->onlyExistingColumns('immediate_outcomes', array_merge([
                    'number' => $number,
                    'title' => 'Immediate Outcome ' . $number . ': ' . $title,
                    'description' => $description,
                ], $this->nowColumns('immediate_outcomes')))
            );
        }
    }
}
