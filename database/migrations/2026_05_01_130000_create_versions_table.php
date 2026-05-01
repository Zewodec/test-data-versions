<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('versions', function (Blueprint $table) {
            $table->id();
            $table->morphs('versionable');
            $table->json('data');
            $table->unsignedInteger('version_number');
            $table->timestamps();

            $table->unique(
                ['versionable_type', 'versionable_id', 'version_number'],
                'versions_versionable_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('versions');
    }
};
