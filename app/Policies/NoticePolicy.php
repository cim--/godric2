<?php

namespace App\Policies;

use App\Models\Notice;
use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class NoticePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can edit notices
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Notice  $notice
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function manage(User $user)
    {
        return $user->member
            ->roles()
            ->whereIn('role', [Role::ROLE_SUPERUSER, Role::ROLE_SECRETARY])
            ->count() > 0;
    }
}
