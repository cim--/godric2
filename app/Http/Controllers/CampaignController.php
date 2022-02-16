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
}
