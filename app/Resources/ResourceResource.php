<?php

namespace App\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray(Request $request)
    {
        return [
            'id'            => $this->ressource_id,
            'title'         => $this->title ?? null,
            'description'   => $this->description ?? null,
            'url'           => $this->url ?? null,
            'created_at'    => $this->created_at ?? null,
            'updated_at'    => $this->updated_at ?? null
        ];
    }
}
