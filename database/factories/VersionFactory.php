<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Version;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class VersionFactory extends Factory
{
    protected $model = Version::class;

    public function definition(): array
    {
        return [
            'versionable_type' => Company::class,
            'versionable_id' => Company::factory(),
            'data' => [
                'name' => $this->faker->company(),
                'address' => $this->faker->address(),
            ],
            'version_number' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
