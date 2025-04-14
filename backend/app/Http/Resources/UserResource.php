<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @property int $id
     * @property string $name
     * @property string $username
     * @property string|null $no_telp
     * @property string $role
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'no_telp' => $this->no_telp,
            'role' => $this->role,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
