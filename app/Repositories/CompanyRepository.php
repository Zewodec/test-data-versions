<?php

namespace App\Repositories;

use App\DTO\StoreCompanyDto;
use App\Models\Company;
use Illuminate\Database\UniqueConstraintViolationException;

class CompanyRepository
{
    public function findOrCreateCompany(
        StoreCompanyDto $storeCompanyDto
    ): Company {
        try {
            return Company::lockForUpdate()->firstOrCreate(
                ['edrpou' => $storeCompanyDto->edrpou],
                [
                    'name' => $storeCompanyDto->name,
                    'address' => $storeCompanyDto->address,
                ]
            );
        } catch (UniqueConstraintViolationException) {
            return Company::where('edrpou', $storeCompanyDto->edrpou)->lockForUpdate()->firstOrFail();
        }
    }
}
