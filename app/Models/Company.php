<?php

namespace App\Models;

use App\HasVersions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory, HasVersions;

    protected $fillable = [
        'name',
        'edrpou',
        'address',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function versions(): HasMany
    {
        return $this->hasMany(Version::class, 'versionable_id')
            ->where('versionable_type', self::class);
    }
}
