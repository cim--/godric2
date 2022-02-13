<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use HasFactory;

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

}
