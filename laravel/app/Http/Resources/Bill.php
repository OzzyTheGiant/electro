<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Bill extends JsonResource {
    /**
     * Transform the Bill resource into an array.
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
			'ID' => $this->ID,
			'User' => $this->User,
            'PaymentAmount' => $this->PaymentAmount,
            'PaymentDate' => $this->PaymentDate
        ];
    }
}