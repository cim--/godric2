<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Ballot extends Model
{
    use HasFactory;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'start' => 'datetime',
        'end' => 'datetime',
    ];
    
    public function options()
    {
        return $this->hasMany(Option::class);
    }

    public function members()
    {
        return $this->belongsToMany(Member::class);
    }

    public function scopeCompleted($q)
    {
        return $q->where('end', '<=', Carbon::now());
    }

    public function scopeIncomplete($q)
    {
        return $q->where('end', '>', Carbon::now());
    }

    public function started()
    {
        if (!$this->id) {
            return false;
        }
        return $this->start->isPast();
    }

    public function ended()
    {
        if (!$this->id) {
            return false;
        }
        return $this->end->isPast();
    }
}
