<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Campaign;
use App\Models\Action;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class MembersController extends Controller
{

    // member list that this user can see
    private function getMemberList()
    {
        $roles = \Auth::user()->member->roles;

        $campaign = Campaign::started()->count();

        $members = Member::orderBy('lastname')->orderBy('firstname');
        $hasrole = false;
        foreach ($roles as $role) {
            if ($role->role == Role::ROLE_CAMPAIGNER && $campaign > 0) {
                // proceed
            } elseif ($role->role != Role::ROLE_SUPERUSER && $role->role != Role::ROLE_REP) {
                continue; // not a role for this list
            }  
            $hasrole = true;
            if ($role->restrictfield == "") {
                // trivially true
                $members->orWhere('id', '>', '0');
                break;
            } else {
                $members->orWhere($role->restrictfield, $role->restrictvalue);
            }
        }
        if (!$hasrole) {
            // no list - though shouldn't get here anyway
            return collect([]);
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
        if (!$user->can('viewFull', $member)) {
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
        if (!$user->can('viewFull', $member)) {
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

    public function setPassword(Member $member, Request $request)
    {
        $user = \Auth::user();
        if (!$user->can('setPassword', $member)) {
            abort(403);
        }

        $newpass = $request->input('newpass');
        if (strlen($newpass) < 8) {
            return back()->with('message', 'Password must be at least 8 characters');
        }
        
        $member->user->password = Hash::make($newpass);
        $member->user->save();

        return back()->with('message', 'Temporary Password set - please contact the member to confirm this promptly.');
    }
    
    public function export(Request $request)
    {
        $members = $this->getMemberList();
        $pastcampaigns = Campaign::ended()->orderBy('end')->get();

        $full = $request->input('full', 0);
        if ($full == 0) {
            $campaigns = Campaign::started()->orderBy('end')->get();
        } else {
            $campaigns = [];
        }

        $format = $request->input('format');
        switch ($format) {
        case "email":
            $data = $this->exportEmail($members, $campaigns);
            break;
        case "phone":
            $data = $this->exportPhone($members, $campaigns);
            break;
        case "thrutext":
            $data = $this->exportThrutext($members, $campaigns);
            break;
        case "rep":
            $data = $this->exportRep($members, $pastcampaigns, $campaigns);
            break;
        default:
            abort(400);
        }

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=".$format.".csv",
        ];

        $csvencoder = function() use($data) {
            $file = fopen('php://output', 'w');
            foreach ($data as $line) {
                fputcsv($file, $line);
            }
            fclose($file);
        };
        
        return response()->stream($csvencoder, 200, $headers);
    }

    private function exportEmail($members, $campaigns)
    {
        $data = [];
        $data[] = ["EMAIL", "FNAME", "LNAME"];

        foreach ($members as $member) {
            if (count($campaigns) > 0) {
                foreach ($campaigns as $campaign) {
                    $part = $campaign->participation($member);
                    if ($part == "yes" || $part == "no") {
                        continue; // try next campaign
                    } elseif ($campaign->votersonly && !$member->voter) {
                        continue; // try next campaign
                    } else {
                        $data[] = [$member->email, $member->firstname, $member->lastname];
                        continue 2;
                        // next member
                    }
                }
            } else {
                // include all
                $data[] = [$member->email, $member->firstname, $member->lastname];
            }
        }
        return $data;
    }

    private function exportPhone($members, $campaigns)
    {
        $data = [];
        $data[] = ["FNAME", "LNAME", "DEPT", "PHONE"];

        foreach ($members as $member) {
            if (count($campaigns) > 0) {
                foreach ($campaigns as $campaign) {
                    $part = $campaign->participation($member);
                    if ($part == "yes" || $part == "no") {
                        continue; // try next campaign
                    } elseif ($campaign->votersonly && !$member->voter) {
                        continue; // try next campaign
                    } else {
                        $data[] = [$member->firstname, $member->lastname, $member->department, $member->mobile];
                        continue 2;
                        // next member
                    }
                }
            } else {
                // include all
                $data[] = [$member->firstname, $member->lastname, $member->department, $member->mobile];
            }
        }
        return $data;
    }

    private function exportThrutext($members, $campaigns)
    {
        $data = [];
        $data[] = ["FNAME", "LNAME", "DEPT", "PHONE"];

        foreach ($members as $member) {
            if ($member->hasMobileNumber()) {
                if (count($campaigns) > 0) {
                    foreach ($campaigns as $campaign) {
                        $part = $campaign->participation($member);
                        if ($part == "yes" || $part == "no") {
                            continue; // try next campaign
                        } elseif ($campaign->votersonly && !$member->voter) {
                            continue; // try next campaign
                        } else {
                            $data[] = [$member->firstname, $member->lastname, $member->department, $member->mobile];
                            continue 2;
                            // next member
                        }
                    }
                } else {
                    // include all
                    $data[] = [$member->firstname, $member->lastname, $member->department, $member->mobile];
                }
            }
        }
        return $data;
    }

    private function exportRep($members, $pastcampaigns, $campaigns)
    {
        $data = [];
        $headers = ["Member ID", "First name", "Last name", "Email", "Phone", "Department", "Job Type", "Member Type", "Voter?"];
        foreach ($pastcampaigns as $pc) {
            $headers[] = "(P)".$pc->name;
        }
        foreach ($campaigns as $c) {
            $headers[] = $c->name;
        }
        $data[] = $headers;

        foreach ($members as $member) {
            $row = [
                $member->membership,
                $member->firstname,
                $member->lastname,
                $member->email,
                $member->mobile,
                $member->department,
                $member->jobtype,
                $member->membertype,
                $member->voter ? "Yes":"No",
            ];

            foreach ($pastcampaigns as $pc) {
                $row[] = $pc->participation($member);
            }
            foreach ($campaigns as $c) {
                $row[] = $c->participation($member);
            }
            $data[] = $row;
        }
        return $data;
    }


    public function search()
    {
        if (!Campaign::started()->count()) {
            return view('phonebank.nocampaign');
        }
        
        return view('phonebank.search', [
            'search' => '',
            'results' => null,
            'campaigns' => null
        ]);
    }

    public function doSearch(Request $request)
    {
        $campaigns = Campaign::started()->get();
        if (!$campaigns->count()) {
            return view('phonebank.nocampaign');
        }
        
        $user = Auth::user();
        
        $search = $request->input('search');
        $words = explode(" ", trim($search));
        if (strlen(trim($search)) < 3) {
            return redirect()->route('phonebank')->with('message', 'Search term must be at least three characters');
        }
        
        $members = Member::orderBy('lastname');
        foreach ($words as $word) {
            $members->where(function ($q) use ($word) {
                $s = "%".trim($word)."%";
                $q->where('firstname', 'LIKE', $s)
                  ->orWhere('lastname', 'LIKE', $s)
                  ->orWhere('email', 'LIKE', $s)
                  ->orWhere('mobile', 'LIKE', $s)
                  ->orWhere('membership', 'LIKE', $s);
            });
        }
        $people = $members->get();
        $results = [];
        foreach ($people as $person) {
            if ($user->can('view', $person)) { // not viewFull
                $results[] = $person;
            }
        }
        
        return view('phonebank.search', [
            'search' => $search,
            'results' => $results,
            'campaigns' => $campaigns
        ]);
    }

    public function setParticipation(Request $request, Member $member)
    {
        if (!Campaign::started()->count()) {
            return view('phonebank.nocampaign');
        }

        $user = Auth::user();

        if (!$member || !$user->can('view', $member)) {
            return redirect()->route('phonebank')->with('message', 'This member could not be found');
        }
        
        $campaigns = Campaign::started()->get();
        foreach ($campaigns as $campaign) {
            if ($campaign->votersonly && !$member->voter) {
                // skip this one
                continue;
            }

            $action = Action::firstOrNew([
                'campaign_id' => $campaign->id,
                'member_id' => $member->id
            ]);
            $part = $request->input('part'.$campaign->id);
            $action->action = $part;
            $action->save();
        }

        return redirect()->route('phonebank')->with('message', $member->firstname." ".$member->lastname." participation updated");
    }
}
