<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    public function ballot()
    {
        return $this->belongsTo(Ballot::class);
    }

    public function percent()
    {
        return number_format(100 * $this->votes / $this->ballot->total(), 1);
    }
}
