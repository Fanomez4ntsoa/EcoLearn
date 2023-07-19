<?php

namespace App\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAccessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return void
     */
    public function toArray(Request $request)
    {
        return [
            'id'            => $this->access->access_id,
            'key'           => $this->access->key ?? null,
            'description'   => $this->access->name ?? null,
        ];
    }
}