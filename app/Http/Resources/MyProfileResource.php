<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MyProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        // print_r($this->resource->toArray());
        // exit;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role_access_type' => $this->role_access_type,
            'role' => new RoleResource($this->whenLoaded('role')),
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'permissions' => $this->resource->permissions->toArray(),
        ];
    }
}
