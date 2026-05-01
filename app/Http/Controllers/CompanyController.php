<?php

namespace App\Http\Controllers;

use App\DTO\StoreCompanyDto;
use App\Enum\DataVersioningStatus;
use App\Http\Requests\CompanyRequest;
use App\Http\Resources\VersioningResultResponse;
use App\Models\Company;
use App\Services\CompanyService;

class CompanyController extends Controller
{
    public function __construct(
        protected CompanyService $companyService,
    ) {}

    public function store(CompanyRequest $request)
    {
        $data = StoreCompanyDto::from($request->validated());

        $company = $this->companyService->findOrCreateCompany($data);

        if ($company->wasRecentlyCreated) {
            return new VersioningResultResponse([
                'status' => DataVersioningStatus::CREATED->value,
                'company_id' => $company->id,
                'version' => $company->version,
            ]);
        } else {
            $result = $this->companyService->updateCompany($company, $data);

            return new VersioningResultResponse($company);
        }
    }

    public function showVersions(Company $company)
    {
        return new VersioningResultResponse($company);
    }
}
