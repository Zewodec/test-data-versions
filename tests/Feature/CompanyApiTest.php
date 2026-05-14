<?php

namespace Tests\Feature;

use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyApiTest extends TestCase
{
    use RefreshDatabase;

    // POST /api/companies

    public function test_creates_new_company_with_version(): void
    {
        $response = $this->postJson('/api/companies', [
            'name' => 'Test Company',
            'edrpou' => '1234567890',
            'address' => '123 Test Street',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['data' => ['status', 'company_id', 'version']])
            ->assertJson([
                'data' => [
                    'status' => 'created',
                    'version' => 1,
                ],
            ]);

        $this->assertDatabaseHas('companies', [
            'edrpou' => '1234567890',
            'name' => 'Test Company',
        ]);

        $this->assertDatabaseCount('versions', 1);
    }

    public function test_create_returns_correct_company_id(): void
    {
        $response = $this->postJson('/api/companies', [
            'name' => 'Test Company',
            'edrpou' => '1234567890',
            'address' => '123 Test Street',
        ]);

        $company = Company::where('edrpou', '1234567890')->first();

        $response->assertJson(['data' => ['company_id' => $company->id]]);
    }

    public function test_updates_existing_company(): void
    {
        $company = Company::factory()->create([
            'edrpou' => '1234567890',
            'name' => 'Old Name',
            'address' => 'Old Address',
        ]);

        $response = $this->postJson('/api/companies', [
            'name' => 'New Name',
            'edrpou' => '1234567890',
            'address' => 'New Address',
        ]);

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'status' => 'updated',
                    'company_id' => $company->id,
                    'version' => 2,
                ],
            ]);

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'New Name',
            'address' => 'New Address',
        ]);

        $this->assertDatabaseCount('versions', 2);
    }

    public function test_update_on_name_change_only(): void
    {
        $company = Company::factory()->create([
            'edrpou' => '1234567890',
            'name' => 'Old Name',
            'address' => 'Same Address',
        ]);

        $response = $this->postJson('/api/companies', [
            'name' => 'New Name',
            'edrpou' => '1234567890',
            'address' => 'Same Address',
        ]);

        $response->assertOk()->assertJson(['data' => ['status' => 'updated', 'version' => 2]]);
        $this->assertDatabaseCount('versions', 2);
    }

    public function test_update_on_address_change_only(): void
    {
        $company = Company::factory()->create([
            'edrpou' => '1234567890',
            'name' => 'Same Name',
            'address' => 'Old Address',
        ]);

        $response = $this->postJson('/api/companies', [
            'name' => 'Same Name',
            'edrpou' => '1234567890',
            'address' => 'New Address',
        ]);

        $response->assertOk()->assertJson(['data' => ['status' => 'updated', 'version' => 2]]);
        $this->assertDatabaseCount('versions', 2);
    }

    public function test_returns_duplicated_when_data_unchanged(): void
    {
        $company = Company::factory()->create([
            'edrpou' => '1234567890',
            'name' => 'Same Name',
            'address' => 'Same Address',
        ]);

        $response = $this->postJson('/api/companies', [
            'name' => 'Same Name',
            'edrpou' => '1234567890',
            'address' => 'Same Address',
        ]);

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'status' => 'duplicated',
                    'company_id' => $company->id,
                    'version' => 1,
                ],
            ]);

        $this->assertDatabaseCount('versions', 1);
    }

    public function test_validates_required_fields(): void
    {
        $this->postJson('/api/companies', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'edrpou', 'address']);
    }

    public function test_name_max_length_validation(): void
    {
        $this->postJson('/api/companies', [
            'name' => str_repeat('A', 255),
            'edrpou' => '1111111111',
            'address' => 'Some Address',
        ])->assertOk()->assertJson(['data' => ['status' => 'created']]);

        $this->postJson('/api/companies', [
            'name' => str_repeat('B', 256),
            'edrpou' => '2222222222',
            'address' => 'Some Address',
        ])->assertOk()->assertJson(['data' => ['status' => 'created']]);

        $this->postJson('/api/companies', [
            'name' => str_repeat('C', 257),
            'edrpou' => '3333333333',
            'address' => 'Some Address',
        ])->assertUnprocessable()->assertJsonValidationErrors(['name']);
    }

    public function test_edrpou_max_length_validation(): void
    {
        $this->postJson('/api/companies', [
            'name' => 'Test',
            'edrpou' => '12345678901',
            'address' => 'Some Address',
        ])->assertUnprocessable()->assertJsonValidationErrors(['edrpou']);
    }

    // GET /api/companies/{edrpou}/versions

    public function test_returns_company_with_versions(): void
    {
        $company = Company::factory()->create([
            'name' => 'Versioned Co',
            'edrpou' => '5555555555',
            'address' => '1 Version St',
        ]);

        $company->update(['name' => 'Updated Co']);
        $company->snapshot();

        $response = $this->getJson("/api/companies/{$company->edrpou}/versions");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => ['company_id', 'name', 'address', 'version', 'versions'],
            ])
            ->assertJson([
                'data' => [
                    'company_id' => $company->id,
                    'name' => 'Updated Co',
                    'version' => 2,
                ],
            ]);

        $this->assertCount(2, $response->json('data.versions'));
    }

    public function test_versions_contain_historical_data(): void
    {
        $company = Company::factory()->create([
            'name' => 'Original Name',
            'edrpou' => '5555555555',
            'address' => 'Original Address',
        ]);

        $company->update(['name' => 'Changed Name']);
        $company->snapshot();

        $versions = $this->getJson("/api/companies/{$company->edrpou}/versions")
            ->json('data.versions');

        $this->assertEquals(2, $versions[0]['version']);
        $this->assertEquals('Changed Name', $versions[0]['name']);
        $this->assertEquals(1, $versions[1]['version']);
        $this->assertEquals('Original Name', $versions[1]['name']);
    }

    public function test_returns_404_for_nonexistent_company(): void
    {
        $this->getJson('/api/companies/9999999999/versions')->assertNotFound();
    }
}
