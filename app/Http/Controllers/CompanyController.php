<?php

namespace App\Http\Controllers;

use App\DTO\StoreCompanyDto;
use App\Enum\DataVersioningStatus;
use App\Http\Requests\CompanyRequest;
use App\Http\Resources\CompanyVersionsResource;
use App\Http\Resources\VersioningResultResponse;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    public function __construct(
        protected CompanyService $companyService,
    ) {}

    public function store(CompanyRequest $request)
    {
        $data = StoreCompanyDto::from($request->validated());

        try {
            return DB::transaction(function () use ($data) {
                $company = $this->companyService->findOrCreateCompany($data);

                if ($company->wasRecentlyCreated) {
                    return new VersioningResultResponse([
                        'status'     => DataVersioningStatus::CREATED->value,
                        'company_id' => $company->id,
                        'version'    => $company->version,
                    ]);
                } else {
                    $result = $this->companyService->updateCompany($company, $data);

                    return new VersioningResultResponse($result);
                }
            });
        } catch (\Throwable $e) {
            Log::error('Error storing company: ' . $e->getMessage());
            // send error message with code
            return response()->json([
                'message' => 'An error occurred while processing your request.',
            ], 500);
        }
    }

    public function showVersions(Company $company)
    {
        $company->load('versions');

        return new CompanyVersionsResource($company);
    }
}
