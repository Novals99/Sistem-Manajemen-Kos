<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isStaff();
    }

    public function rules(): array
    {
        return [
            'room_number' => ['required', 'string', 'max:10', Rule::unique('rooms')->ignore($this->room)],
            'floor' => ['required', 'integer', 'min:1', 'max:99'],
            'type' => ['required', 'in:single,double,suite'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:available,occupied,maintenance,reserved'],
            'description' => ['nullable', 'string', 'max:1000'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['string', 'max:50'],
            'max_occupants' => ['required', 'integer', 'min:1', 'max:10'],
        ];
    }
}
