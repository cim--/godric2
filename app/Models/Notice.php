<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notice extends Model
{
    use HasFactory;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    public function scopeHighlighted($q)
    {
        $q->where('highlight', true);
    }

    public function scopeCurrent($q)
    {
        return $q->where(function ($sq) {
            $sq->whereDate('start', '<=', Carbon::now())
               ->orWhereNull('start');
        })->where(function ($eq) {
            $eq->whereDate('end', '>=', Carbon::now())
               ->orWhereNull('end');
        });
    }

    public function isCurrent()
    {
        if ($this->start !== null && $this->start->isFuture()) {
            return false;
        }
        if ($this->end !== null && $this->end->copy()->setTime(23,59,59)->isPast()) {
            return false;
        }
        return true;
    }
}
