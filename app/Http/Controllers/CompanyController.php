<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;

class CompanyController extends Controller
{
    public function index()
    {
        return CompanyResource::collection(Company::all());
    }

    public function store(CompanyRequest $request)
    {
        return new CompanyResource(Company::create($request->validated()));
    }

    public function showVersions(Company $company)
    {
        return new CompanyResource($company);
    }
}
