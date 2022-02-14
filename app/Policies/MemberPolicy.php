<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class MemberPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function seeReports(User $user)
    {
        return $user->member->roles()->where('role', Role::ROLE_REP)
                    ->orWhere('role', Role::ROLE_SUPERUSER)
                    ->orWhere('role', Role::ROLE_COMMS)->count() > 0;
    }

    public function seeCharts(User $user)
    {
        return $user->member->roles()->where('role', Role::ROLE_REP)
                    ->orWhere('role', Role::ROLE_SUPERUSER)
                    ->count() > 0;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Member $member)
    {
        if ($user->member->id == $member->id) {
            return true;
        }

        $roles = $user->member->roles;
        foreach ($roles as $role) {
            if ($role->role == Role::ROLE_SUPERUSER) {
                return true;
            } else {
                if (!$role->restrictfield) {
                    // view all
                    return true;
                } 
                $field = $role->restrictfield;
                if ($member->$field == $role->restrictvalue) {
                    // view scope
                    return true;
                }
            }
        }
        return false;
    }

    // superuser wide-scale management
    public function manage(User $user)
    {
        return $user->member->roles()->where('role', Role::ROLE_SUPERUSER)->count() > 0;
    }

}
