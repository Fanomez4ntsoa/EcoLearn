<?php

namespace App\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id'        => $this->user_id,
            'email'     => $this->email ?? null,
            'firstname' => $this->name ?? null,
            'lastname'  => $this->username ?? null,
            'createdAt' => $this->created_at ?? null,
            'validity' => [
                'from' => $this->Valid_From,
                'till' => $this->Valid_Till,
            ],
        ];
    }
}