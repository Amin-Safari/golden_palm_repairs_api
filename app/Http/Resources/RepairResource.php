<?php

// app/Http/Resources/RepairResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RepairResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'device_name' => $this->device_name,
            'device_problem' => $this->device_problem,
            'status' => $this->status,
            'price' => $this->price,
            'verification_code' => $this->when(
                $this->status === 'ready_for_delivery' || $this->status === 'delivered',
                $this->verification_code
            ),
            'received_at' => $this->received_at_shamsi,
            'delivered_at' => $this->delivered_at_shamsi,
            'customer' => [
                'name' => $this->customer->name,
                'phone' => $this->customer->phone
            ],
            'technician' => $this->when($this->technician, [
                'name' => $this->technician?->name,
                'phone' => $this->technician?->phone
            ]),
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name
            ],
            'appearance_details' => $this->appearance_details,
            'repair_details' => $this->repair_details,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
