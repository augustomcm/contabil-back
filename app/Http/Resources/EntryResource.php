<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EntryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'status' => $this->status,
            'description' => $this->description,
            'isExpense' => $this->isExpense(),
            'value' => $this->value->getAmountFloat(),
            'paymentType' => $this->payment_type,
            'isPaid' => $this->isPaid()
        ];
    }
}
