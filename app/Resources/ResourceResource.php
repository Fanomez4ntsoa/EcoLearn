<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ResourceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => $request->id,
            'category_id'   => $request->category_id ?? null,
            'name'          => $request->title ?? null,
            'description'   => $request->description ?? null,
            'url'           => $request->url ?? null,
            'created_at'    => $request->created_at ?? null
        ];
    }
}
