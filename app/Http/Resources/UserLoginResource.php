<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserLoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'access_token' => $this['token'],
            'token_type' => 'Bearer',
            'expires_in' => $this['expires_in'],
            'user' => [
                'id' => $this['user']['id'],
                'name' => $this['user']['name'],
                'email' => $this['user']['email'],

                "role" => [
                    'id' => $this['user']['role']['id'],
                    'name' => $this['user']['role']['name'],
                    'display_name' => $this['user']['role']['display_name'],
                ]
            ]
        ];
    }
}
