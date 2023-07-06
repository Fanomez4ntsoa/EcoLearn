<?php

namespace App\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return void
     */
    public function toArray($request)
    {
        return [
            'id'            => $request->id,
            'name'          => $request->name ?? null,
            'description'   => $request->description ?? null,
            'created_at'    => $request->created_at ?? null
        ];
    }
}
