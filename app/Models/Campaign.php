<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Campaign extends Model
{
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    
    use HasFactory;

    public function actions()
    {
        return $this->hasMany(Action::class);
    }
    
    public function scopeStarted($q)
    {
        $q->whereDate('start', '<=', Carbon::now())
          ->whereDate('end', '>=', Carbon::now());
    }

    public function scopeEnded($q)
    {
        $q->whereDate('end', '<', Carbon::now());
    }


    public function participation(Member $member)
    {
        $action = $this->actions->where('member_id', $member->id)->first();
        if (!$action) {
            return "-";
        } else {
            return $action->action;
        }
    }
}
