<?php

namespace App\Services;

use App\DTO\StoreCompanyDto;
use App\Enum\DataVersioningStatus;
use App\Models\Company;
use App\Repositories\CompanyRepository;

class CompanyService
{
    public function __construct(
        protected CompanyRepository $companyRepository,
    ) {}

    public function findOrCreateCompany(StoreCompanyDto $storeCompanyDto): Company
    {
        return $this->companyRepository->findOrCreateCompany($storeCompanyDto);
    }

    public function updateCompany(Company $company, StoreCompanyDto $data): array
    {
        $incoming = [
            'name' => $data->name,
            'address' => $data->address,
        ];

        $current = $company->only(['name', 'address']);

        if ($incoming === $current) {
            return [
                'status' => DataVersioningStatus::DUPLICATED->value,
                'company_id' => $company->id,
                'version' => $company->version,
            ];
        }

        $company->update($incoming);
        $company->snapshot();

        return [
            'status' => DataVersioningStatus::UPDATED->value,
            'company_id' => $company->id,
            'version' => $company->version,
        ];
    }
}
