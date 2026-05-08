<?php

namespace App\Repositories;

use App\DTO\StoreCompanyDto;
use App\Models\Company;

class CompanyRepository
{
    public function findOrCreateCompany(
        StoreCompanyDto $storeCompanyDto
    ): Company {
        return Company::lockForUpdate()->firstOrCreate(
            ['edrpou' => $storeCompanyDto->edrpou],
            [
                'name' => $storeCompanyDto->name,
                'address' => $storeCompanyDto->address,
            ]
        );
    }
}
