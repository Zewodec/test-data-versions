<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyVersionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'company_id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'version' => $this->version,
            'versions' => $this->versions->map(function ($version) {
                return [
                    'version' => $version->version,
                    'name' => $version->name,
                    'address' => $version->address,
                    'created_at' => $version->created_at,
                ];
            }),
        ];
    }
}
