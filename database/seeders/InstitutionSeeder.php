<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Database\Seeders\FIUdmsSeederSupport;

class InstitutionSeeder extends Seeder
{
    use FIUdmsSeederSupport;

    public function run(): void
    {
        if (! Schema::hasTable('institutions')) {
            $this->command?->warn('Skipping InstitutionSeeder because the institutions table does not exist.');
            return;
        }

        $institutions = [
            [
                'name' => 'Zimbabwe Revenue Authority',
                'short_name' => 'ZIMRA',
                'sector' => 'Revenue and Customs Administration',
                'email' => 'compliance@zimra.example',
                'phone' => '+263 242 000 001',
                'address' => 'Harare, Zimbabwe',
                'is_active' => true,
            ],
            [
                'name' => 'Zimbabwe Republic Police',
                'short_name' => 'ZRP',
                'sector' => 'Law Enforcement',
                'email' => 'compliance@zrp.example',
                'phone' => '+263 242 000 002',
                'address' => 'Harare, Zimbabwe',
                'is_active' => true,
            ],
            [
                'name' => 'Judicial Service Commission',
                'short_name' => 'JSC',
                'sector' => 'Judiciary',
                'email' => 'compliance@jsc.example',
                'phone' => '+263 242 000 003',
                'address' => 'Harare, Zimbabwe',
                'is_active' => true,
            ],
        ];

        foreach ($institutions as $institution) {
            DB::table('institutions')->updateOrInsert(
                ['name' => $institution['name']],
                $this->onlyExistingColumns('institutions', array_merge($institution, $this->nowColumns('institutions')))
            );
        }
    }
}
