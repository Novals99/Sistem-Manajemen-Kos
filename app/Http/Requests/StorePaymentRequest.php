<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isStaff();
    }

    public function rules(): array
    {
        return [
            'booking_id' => ['required', 'exists:bookings,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,transfer,e-wallet'],
            'payment_type' => ['required', 'in:rent,deposit,penalty,other'],
            'status' => ['required', 'in:pending,paid,failed,refunded'],
            'proof_of_payment' => ['nullable', 'image', 'max:2048'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'period_month' => ['required', 'integer', 'min:1', 'max:12'],
            'period_year' => ['required', 'integer', 'min:2020', 'max:2099'],
        ];
    }
}
