<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    use FIUdmsSeederSupport;

    public function run(): void
    {
        if (! Schema::hasTable('users')) {
            $this->command?->warn('Skipping UserSeeder because the users table does not exist.');
            return;
        }

        $defaultPassword = env('FIUDMS_SEED_PASSWORD', 'Password@12345');

        $users = [
            [
                'name' => 'FIU System Administrator',
                'email' => 'admin@fiu.example',
                'role' => 'fiu_admin',
                'institution_short_name' => null,
            ],
            [
                'name' => 'FIU Compliance Reviewer',
                'email' => 'reviewer@fiu.example',
                'role' => 'fiu_reviewer',
                'institution_short_name' => null,
            ],
            [
                'name' => 'ZIMRA Institutional Representative',
                'email' => 'representative@zimra.example',
                'role' => 'institution_representative',
                'institution_short_name' => 'ZIMRA',
            ],
            [
                'name' => 'ZRP Institutional Representative',
                'email' => 'representative@zrp.example',
                'role' => 'institution_representative',
                'institution_short_name' => 'ZRP',
            ],
            [
                'name' => 'JSC Institutional Representative',
                'email' => 'representative@jsc.example',
                'role' => 'institution_representative',
                'institution_short_name' => 'JSC',
            ],
        ];

        foreach ($users as $user) {
            $institutionId = null;

            if ($user['institution_short_name'] && Schema::hasTable('institutions')) {
                $institutionId = DB::table('institutions')->where('short_name', $user['institution_short_name'])->value('id');
            }

            $payload = [
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make($defaultPassword),
                'role' => $user['role'],
                'institution_id' => $institutionId,
                'email_verified_at' => now(),
            ];

            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                $this->onlyExistingColumns('users', array_merge($payload, $this->nowColumns('users')))
            );
        }
    }
}
