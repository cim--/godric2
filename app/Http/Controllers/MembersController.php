<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Campaign;

class MembersController extends Controller
{

    // member list that this user can see
    private function getMemberList()
    {
        $roles = \Auth::user()->member->roles;

        $members = Member::orderBy('lastname')->orderBy('firstname');
        foreach ($roles as $role) {
            if ($role->restrictfield == "") {
                // trivially true
                $members->orWhere('id', '>', '0');
                break;
            } else {
                $members->orWhere($role->restrictfield, $role->restrictvalue);
            }
        }

        return $members->cursor();
    }

    public function list()
    {
        $members = $this->getMemberList();
        $pastcampaigns = Campaign::ended()->orderBy('end')->get();
        $campaigns = Campaign::started()->orderBy('end')->get();
        
        return view('members.list', [
            'members' => $members,
            'pastcampaigns' => $pastcampaigns,
            'campaigns' => $campaigns
        ]);
    }
    
}
