<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Version;
use Illuminate\Database\Seeder;

class VersionSeeder extends Seeder
{
    public function run(): void
    {
        Company::all()->each(function (Company $company) {
            $count = rand(1, 3);

            for ($i = 0; $i < $count; $i++) {
                Version::factory()->create([
                    'versionable_type' => Company::class,
                    'versionable_id' => $company->id,
                    'version_number' => $company->versions()->max('version_number') + 1,
                ]);
            }
        });
    }
}
