<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->hasOne(User::class, 'username', 'membership');
    }

    public function actions()
    {
        return $this->hasMany(Action::class);
    }

    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    public function participation(Campaign $campaign)
    {
        $action = $this->actions->where('campaign_id', $campaign->id)->first();
        if (!$action) {
            return "-";
        } else {
            return $action->action;
        }
    }

    public function scopeVoter($q)
    {
        return $q->where('voter', true);
    }
    
    public function hasMobileNumber()
    {
        $number = $this->mobile;
        $number = preg_replace("/[^0-9]+/", "", $number);
        if (substr($number, 0, 2) == "07" || substr($number, 0, 3) == "447") {
            return true;
        }
        return false;
    }
}
