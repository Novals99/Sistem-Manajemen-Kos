<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'reported_by',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'resolved_at',
        'cost',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
            'cost' => 'decimal:2',
        ];
    }

    // ── Relationships ────────────────────────────────

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    // ── Helpers ──────────────────────────────────────

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function isPending(): bool
    {
        return in_array($this->status, ['reported', 'in_progress']);
    }
}
