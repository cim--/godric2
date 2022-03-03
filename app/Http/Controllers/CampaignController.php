<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Member;
use App\Models\Action;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $campaigns = Campaign::withCount(['actions' => function ($q) {
            $q->where('action', 'yes');
        }])->orderBy('end', 'DESC')->get();
        $membercount = Member::count();
        $votercount = Member::where('voter', true)->count();
        return view('campaigns.index', [
            'campaigns' => $campaigns,
            'members' => $membercount,
            'voters' => $votercount
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('campaigns.form', [
            'campaign' => new Campaign
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->update($request, new Campaign);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Campaign  $campaign
     * @return \Illuminate\Http\Response
     */
    public function edit(Campaign $campaign)
    {
        return view('campaigns.form', [
            'campaign' => $campaign
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Campaign  $campaign
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Campaign $campaign)
    {
        $campaign->name = $request->input('name');
        $campaign->description = $request->input('description');
        $campaign->start = Carbon::parse($request->input('start'));
        $campaign->end = Carbon::parse($request->input('end'));
        $campaign->target = $request->input('target');
        $campaign->votersonly = $request->input('votersonly', false);
        if ($campaign->end->isFuture()) {
            if ($campaign->votersonly) {
                $count = Member::where('voter', true)->count();
            } else {
                $count = Member::count();
            }
            $campaign->calctarget = ceil($campaign->target * $count / 100);
        } elseif (!$campaign->calctarget) {
            $campaign->calctarget = 0;
        }
        $campaign->save();

        return redirect()->route('campaigns.index')->with('message', 'Campaign edited');
    }



    public function participate(Request $request, Campaign $campaign)
    {
        $part = $request->input('participation'.$campaign->id, '-');

        $self = \Auth::user()->member;
        if ($campaign->start->isFuture() || $campaign->end->copy()->addDay()->isPast()) {
            return back()->with('message', 'This campaign is not currently active');
        }
        if ($campaign->votersonly && !$self->voter) {
            return back()->with('message', 'This campaign is for voters only');
        }

        if ($part == "-") {
            return back()->with('message', 'You have not selected an option');
        }

        $action = Action::firstOrNew([
            'campaign_id' => $campaign->id,
            'member_id' => $self->id
        ]);
        $action->action = $part;
        $action->save();

        return back()->with('message', 'Your participation has been updated');
    }


    public function bulkImport(Campaign $campaign)
    {
        return view('campaigns.bulkimport', [
            'campaign' => $campaign
        ]);
    }

    public function bulkImportProcess(Campaign $campaign, Request $request)
    {
        $part = $request->input('action');
        $members = explode("\n", $request->input('members'));
        $notfound = [];
        foreach ($members as $memberid) {
            $memberid = trim($memberid);

            if ($memberid == "") {
                continue;
            }
            
            $member = Member::where('membership', $memberid)
                    ->orWhere('email', $memberid)
                    ->orWhere('mobile', $memberid)
                    ->first();
            if ($member) {
                $action = Action::firstOrNew([
                    'campaign_id' => $campaign->id,
                    'member_id' => $member->id
                ]);
                $action->action = $part;
                $action->save();
            } else {
                $notfound[] = $memberid;
            }
        }

        if (count($notfound) == 0) {
            return redirect()->route('campaigns.index')->with('message', 'Imported actions');
        } else {
            return redirect()->route('campaigns.index')->with('message', 'Imported actions. Some IDs not found: '.join(",", $notfound));
        }
    }

    public function reportIndex()
    {
        $campaigns = Campaign::withCount(['actions' => function ($q) {
            $q->where('action', 'yes');
        }])->orderBy('end', 'DESC')->get();
        return view('campaigns.report.index', [
            'campaigns' => $campaigns
        ]);
    }

    public function reportView(Campaign $campaign)
    {
        $departments = [];
        if ($campaign->votersonly) {
            $members = Member::where('voter', true)->get();
        } else {
            $members = Member::all();
        }

        $mcount = 0;
        $pcount = 0;
        foreach ($members as $member) {
            $dept = $member->department;
            if (!isset($departments[$dept])) {
                $departments[$dept] = [
                    'members' => 0,
                    'participants' => 0
                ];
            }
            $departments[$dept]['members']++;
            $mcount++;
            if ($campaign->participation($member) == "yes") {
                $departments[$dept]['participants']++;
                $pcount++;
            }
        }
        ksort($departments);
        
        return view('campaigns.report.show', [
            'campaign' => $campaign,
            'departments' => $departments,
            'mcount' => $mcount,
            'pcount' => $pcount
        ]);
    }

    public function destroy(Request $request, Campaign $campaign)
    {
        if ($request->input('confirm') == $campaign->name) {
            $campaign->actions()->delete();
            $campaign->delete();
            return redirect()->route('campaigns.index')->with('message', 'Campaign Deleted');
        } else {
            return back()->with('message', 'Deletion confirmation not given');
        }
    }

}
