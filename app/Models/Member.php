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

}
