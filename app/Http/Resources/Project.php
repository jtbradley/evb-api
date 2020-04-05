<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Project extends JsonResource
{

    public function toArray($request)
    {
        return parent::toArray($request);

        // return [
        //     'trello_id' => $this->trello_id,
        //     'name' => $this->name,
        //     'user' => $this->user,
        //     'created_at' => $this->created_at->diffForHumans()
        // ];
    }
}
