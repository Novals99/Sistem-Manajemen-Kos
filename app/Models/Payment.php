<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_code',
        'booking_id',
        'tenant_id',
        'amount',
        'payment_date',
        'payment_method',
        'payment_type',
        'status',
        'proof_of_payment',
        'notes',
        'period_month',
        'period_year',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    // ── Relationships ────────────────────────────────

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    // ── Code Generation ──────────────────────────────

    public static function generatePaymentCode(): string
    {
        $date = now()->format('Ymd');
        $lastPayment = static::where('payment_code', 'like', "PAY-{$date}-%")
            ->orderBy('payment_code', 'desc')
            ->first();

        if ($lastPayment) {
            $lastNumber = (int) substr($lastPayment->payment_code, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return "PAY-{$date}-{$newNumber}";
    }

    // ── Helpers ──────────────────────────────────────

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function periodLabel(): string
    {
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];

        return ($months[$this->period_month] ?? '') . ' ' . $this->period_year;
    }
}
