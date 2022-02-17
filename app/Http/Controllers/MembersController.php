<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Campaign;
use App\Models\Action;

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

    public function edit(Member $member)
    {
        $user = \Auth::user();
        if (!$user->can('view', $member)) {
            abort(403);
        }
        
        $campaigns = Campaign::started()->orderBy('end')->get();
        return view('members.edit', [
            'member' => $member,
            'campaigns' => $campaigns
        ]);
    }

    public function update(Member $member, Request $request)
    {
        $user = \Auth::user();
        if (!$user->can('view', $member)) {
            abort(403);
        }
        
        $campaigns = Campaign::started()->orderBy('end')->get();
        foreach ($campaigns as $campaign) {
            $part = $request->input('action'.$campaign->id, "-");
            if ($part != "-") {
                $action = Action::firstOrNew([
                    'campaign_id' => $campaign->id,
                    'member_id' => $member->id
                ]);
                $action->action = $part;
                $action->save();
            } else {
                Action::where('campaign_id', $campaign->id)->where('member_id', $member->id)->delete();
            }
        }

        return redirect()->route('members.list')->with('message', 'Updated campaign participation');
    }
    
    
}
