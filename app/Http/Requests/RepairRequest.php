<?php

// app/Http/Requests/RepairRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RepairRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'device_category_id' => 'required|exists:device_categories,id',
            'device_name' => 'required|string|max:255',
            'device_problem' => 'required|string',
            'appearance_details' => 'nullable|string',
            'repair_details' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'status' => 'sometimes|in:received,in_progress,ready_for_delivery,delivered,canceled',
        ];
    }
}