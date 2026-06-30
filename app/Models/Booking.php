<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'tenant_id',
        'room_id',
        'check_in_date',
        'check_out_date',
        'duration_months',
        'monthly_rate',
        'deposit',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'monthly_rate' => 'decimal:2',
            'deposit' => 'decimal:2',
        ];
    }

    // ── Relationships ────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // ── Code Generation ──────────────────────────────

    public static function generateBookingCode(): string
    {
        $date = now()->format('Ymd');
        $lastBooking = static::where('booking_code', 'like', "BK-{$date}-%")
            ->orderBy('booking_code', 'desc')
            ->first();

        if ($lastBooking) {
            $lastNumber = (int) substr($lastBooking->booking_code, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return "BK-{$date}-{$newNumber}";
    }

    // ── Helpers ──────────────────────────────────────

    public function totalAmount(): float
    {
        return $this->monthly_rate * $this->duration_months;
    }

    public function totalPaid(): float
    {
        return $this->payments()->where('status', 'paid')->sum('amount');
    }

    public function outstandingBalance(): float
    {
        return $this->totalAmount() - $this->totalPaid();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
