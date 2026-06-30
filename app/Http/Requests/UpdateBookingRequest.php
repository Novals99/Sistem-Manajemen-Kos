<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isStaff();
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['sometimes', 'exists:tenants,id'],
            'room_id' => ['sometimes', 'exists:rooms,id'],
            'check_in_date' => ['sometimes', 'date'],
            'duration_months' => ['sometimes', 'integer', 'min:1', 'max:60'],
            'deposit' => ['sometimes', 'numeric', 'min:0'],
            'status' => ['sometimes', 'in:pending,confirmed,active,completed,cancelled'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
