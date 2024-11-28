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

    public function ballots()
    {
        return $this->belongsToMany(Ballot::class);
    }

    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    public function workplaces()
    {
        return $this->belongsToMany(Workplace::class);
    }

    public function participation(Campaign $campaign)
    {
        $action = $this->actions->where('campaign_id', $campaign->id)->first();
        if (!$action) {
            return '-';
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
        $number = preg_replace('/[^0-9]+/', '', $number);
        if (substr($number, 0, 2) == '07' || substr($number, 0, 3) == '447') {
            return true;
        }
        return false;
    }

    public static function search($memberid)
    {
        $memberid = trim($memberid);
        return Member::where('membership', $memberid)
            ->orWhere('email', $memberid)
            ->orWhere('mobile', $memberid)
            ->first();
    }

    /* Gets the representatives of particular roles for a member */
    public function representatives($roles = null)
    {
        if ($roles === null) {
            $roles = [Role::ROLE_REP, Role::ROLE_SUPERUSER];
        }
        $bydept = Member::whereHas('roles', function ($q) use ($roles) {
            $q->where('restrictfield', 'department')
                ->where('restrictvalue', $this->department)
                ->whereIn('role', $roles);
        })
            ->orderBy('lastname')
            ->get();

        $byjtype = Member::whereHas('roles', function ($q) use ($roles) {
            $q->where('restrictfield', 'jobtype')
                ->where('restrictvalue', $this->jobtype)
                ->whereIn('role', $roles);
        })
            ->orderBy('lastname')
            ->get();

        $bymtype = Member::whereHas('roles', function ($q) use ($roles) {
            $q->where('restrictfield', 'membertype')
                ->where('restrictvalue', $this->membertype)
                ->whereIn('role', $roles);
        })
            ->orderBy('lastname')
            ->get();

        $byworkplace = Member::whereHas('roles', function ($q) use ($roles) {
            $q->where('restrictfield', 'membertype')
                ->whereIn('restrictvalue', $this->workplaces->pluck('name'))
                ->whereIn('role', $roles);
        })
            ->orderBy('lastname')
            ->get();

        $byorganisation = Member::whereHas('roles', function ($q) use ($roles) {
            $q->where(function ($nq) {
                $nq->whereNull('restrictfield')->orWhere('restrictfield', '');
            })->whereIn('role', $roles);
        })
            ->orderBy('lastname')
            ->get();

        return [
            'department' => $bydept,
            'jobtype' => $byjtype,
            'membertype' => $bymtype,
            'workplace' => $byworkplace,
            'organisation' => $byorganisation,
        ];
    }

    public function activeBallots()
    {
        $q = Ballot::open();
        if (!$this->voter) {
            $q->where('votersonly', false);
        }
        $q->whereDoesntHave('members', function ($mq) {
            $mq->where('members.id', $this->id);
        });
        return $q->orderBy('end')->with('options')->get();
    }
}
