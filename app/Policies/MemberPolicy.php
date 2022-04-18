<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;
use App\Models\Campaign;
use App\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class MemberPolicy
{
    use HandlesAuthorization;

    /**
     * Determine high-level access permissions to member data
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function seeReports(User $user)
    {
        return $user->member->roles()
                            ->whereIn('role', [
                                Role::ROLE_REP,
                                Role::ROLE_CAMPAIGNER,
                                Role::ROLE_PHONEBANK,
                                Role::ROLE_SUPERUSER,
                                Role::ROLE_REPORT
                            ])
                            ->count() > 0;
    }

    public function seeLists(User $user)
    {
        $roles = [Role::ROLE_REP, Role::ROLE_SUPERUSER];
        if (Campaign::started()->count() > 0) {
            // only applies during campaigns
            $roles[] = Role::ROLE_CAMPAIGNER;
        }
        return $user->member->roles()
                            ->whereIn('role', $roles)
                            ->count() > 0;
    }

    public function seePhonebank(User $user)
    {
        return $user->member->roles()
                            ->whereIn('role', [Role::ROLE_PHONEBANK, Role::ROLE_SUPERUSER])
                            ->count() > 0;
    }

    
    /**
     * Determine whether the user can view the specific member.
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
            } else if ($role->role == Role::ROLE_REP || $role->role == Role::ROLE_PHONEBANK || $role->role == Role::ROLE_CAMPAIGNER) {
                if (!$role->restrictfield) {
                    // view all
                    return true;
                } 
                $field = $role->restrictfield;
                if ($field == "workplace") {
                    if ($member->workplaces->where('name', $role->restrictvalue)->count() > 0) {
                        return true;
                    }
                } elseif ($member->$field == $role->restrictvalue) {
                    // view scope
                    return true;
                }
            }
        }
        return false;
    }

    /* View a rep's report for the member */
    public function viewFull(User $user, Member $member)
    {
        if ($user->member->id == $member->id) {
            return true;
        }

        $campaign = Campaign::started()->count();
        
        $roles = $user->member->roles;

        foreach ($roles as $role) {
            if ($role->role == Role::ROLE_SUPERUSER) {
                return true;
            } else if ($role->role == Role::ROLE_REP ||
                       ($role->role == Role::ROLE_CAMPAIGNER && $campaign > 0)) {
                if (!$role->restrictfield) {
                    // view all
                    return true;
                } 
                $field = $role->restrictfield;
                if ($field == "workplace") {
                    if ($member->workplaces->where('name', $role->restrictvalue)->count() > 0) {
                        return true;
                    }
                } elseif ($member->$field == $role->restrictvalue) {
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

    // for people who have trouble logging in normally
    public function setPassword(User $user, Member $member)
    {
        if (!$member->user) {
            // needs to start process
            return false;
        }
        if ($member->user->hasTemporaryPassword()) {
            // restricted to superusers as this can allow someone with
            // membership access to log in as someone else. But the
            // superuser permission should be limited to people who
            // are very trusted anyway
            return $this->manage($user);
        }
        // can't override a normal password this way - user must
        // either have tried to log in, or started the password reset
        // process
        return false;
    }

    
}
