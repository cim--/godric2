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
}
