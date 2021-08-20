<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RequestResource extends JsonResource
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
            'id' => $this->id,
            'owner_id' => $this->owner_id,
            'photographer_id' => $this->photographer_id,
            'product' => $this->product,
            'location' => $this->location,
            'LQT' => $this->LQT,
            'HRI' => $request->user()->category == 2 ? ($this->approve == 1 ? $this->HRI : null) : $this->HRI,
            'status' => $this->status,
            'approve' => $this->approve,
            'product_owner' => $this->productOwner,
            'photographer' => $this->photographer,
        ];
    }
}
