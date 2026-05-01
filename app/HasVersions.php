<?php

namespace App;

use App\Models\Version;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasVersions
{
    public static function bootHasVersions(): void
    {
        static::created(fn($model) => $model->snapshot());
    }

    public function versions(): MorphMany
    {
        return $this->morphMany(Version::class, 'versionable');
    }

    public function snapshot(): Version
    {
        $nextVersion = $this->nextVersionNumber();

        return $this->versions()->create([
            'data' => $this->getVersionableAttributes(),
            'version_number' => $nextVersion,
        ]);
    }

    public function latestVersion(): ?Version
    {
        return $this->versions()->latest('version_number')->first();
    }

    public function version(): Attribute
    {
        return Attribute::get(fn () => $this->latestVersion()?->version_number);
    }

    protected function nextVersionNumber(): int
    {
        return ($this->versions()->max('version_number') ?? 0) + 1;
    }

    protected function getVersionableAttributes(): array
    {
        $versionable = property_exists($this, 'versionable')
            ? $this->versionable
            : $this->getFillable();

        return $this->only($versionable);
    }
}
