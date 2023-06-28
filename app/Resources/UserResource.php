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
            'email'     => $this->user->email ?? null,
            'firstname' => $this->user->name ?? null,
            'lastname'  => $this->user->username ?? null,
            'validity'  => [
                'from'  => $this->user->Valid_From,
                'till'  => $this->user->Valid_Till,
            ],
        ];
    }
}