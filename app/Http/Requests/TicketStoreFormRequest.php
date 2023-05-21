<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketStoreFormRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'phone' => 'required|numeric',
            'service_id' => 'required|integer',
            'monitor_group_name' => 'required|string',
            'user_id' => 'required',
            "created_at" => 'required'
        ];
    }
}
