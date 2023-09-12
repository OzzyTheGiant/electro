<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Bill extends JsonResource {
    public function toArray(Request $request): array {
        return [
			'id' => $this->id,
			'user_id' => $this->user_id,
            'payment_amount' => $this->payment_amount,
            'payment_date' => $this->payment_date
        ];
    }
}
