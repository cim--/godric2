<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Changelog extends Model
{
    use HasFactory;

    public function scopeOld($q)
    {
        return $q->where('created_at', '<', Carbon::parse('-14 days'));
    }
}
