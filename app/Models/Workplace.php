<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workplace extends Model
{
    use HasFactory;

    public function members()
    {
        return $this->belongsToMany(Member::class);
    }

    public static function managedBy(User $user)
    {
        $roles = $user->member->roles;
        $names = [];
        foreach ($roles as $role) {
            if ($role->role == Role::ROLE_SUPERUSER) {
                return Workplace::orderBy('name')->get();
            } elseif ($role->restrictfield == 'workplace') {
                $names[] = $role->restrictvalue;
            }
        }
        return Workplace::whereIn('name', $names)->orderBy('name')->get();
    }
}
