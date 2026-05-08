<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Version extends Model
{
    use HasFactory;

    protected $fillable = [
        'versionable_type',
        'versionable_id',
        'data',
        'version_number',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'version_number' => 'integer',
        ];
    }

    public function versionable(): MorphTo
    {
        return $this->morphTo();
    }
}
