<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/companies')->controller(\App\Http\Controllers\CompanyController::class)->name('companies.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::get('/{edrpou}/versions', 'showVersions')->name('versions');
});