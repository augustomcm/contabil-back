<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CreditCardResource extends JsonResource
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
            'description' => $this->description,
            'limit' => $this->getLimit()->getAmountFloat(),
            'closing_day' => $this->closing_day,
            'expiration_day' => $this->expiration_day,
            'currentInvoice' => new InvoiceResource($this->getCurrentInvoice())
        ];
    }
}
