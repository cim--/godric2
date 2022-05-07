<?php

namespace App\Http\Controllers;

use App\Models\Ballot;
use App\Models\Option;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BallotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ballots = Ballot::orderBy('end', 'desc')->with('options', function($q) {
            $q->orderBy('order');
        })->get();

        $membercount = Member::count();
        $votercount = Member::where('voter', true)->count();
        
        return view('ballots.index', [
            'ballots' => $ballots,
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
        return view('ballots.form', [
            'ballot' => new Ballot
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
        return $this->update($request, new Ballot);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Ballot  $ballot
     * @return \Illuminate\Http\Response
     */
    public function edit(Ballot $ballot)
    {
        return view('ballots.form', [
            'ballot' => $ballot
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ballot  $ballot
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ballot $ballot)
    {
        if ($ballot->ended()) {
            return back()->with('message', 'This ballot has ended and cannot be edited');
        }
        if (!$ballot->started()) {
            $end = Carbon::parse($request->input('end'));
            $start = Carbon::parse($request->input('start'));
            if (!$end->isFuture() || !$end->gt($start)) {
                return back()->with('message', 'The end time must be in the future and after the start time.');   
            }

            if (!$start->isFuture()) {
                return back()->with('message', 'The start time must be in the future.');   
            }
            $title = $request->input('title');
            $description = $request->input('description');
            if (!$title || !$description) {
                return back()->with('message', 'A title and description are required.');
            }
            $votersonly = (bool)$request->input('votersonly', false);
            $options = explode("\n", $request->input('options'));
            if (count($options) < 2) {
                return back()->with('message', 'At least two options are required.');
            }

            $ballot->title = $title;
            $ballot->description = $description;
            $ballot->start = $start;
            $ballot->end = $end;
            $ballot->votersonly = $votersonly;
            $ballot->save();

            $ballot->options()->delete();
            foreach ($options as $idx => $option) {
                $opt = new Option;
                $opt->option = $option;
                $opt->ballot_id = $ballot->id;
                $opt->votes = 0;
                $opt->order = $idx+1;
                $opt->save();
            }
            return redirect()->route('ballots.index')->with('message', 'Ballot saved');     
        } else {
            $end = Carbon::parse($request->input('end'));
            $start = $ballot->start;
            if (!$end->isFuture() || !$end->gt($start)) {
                return back()->with('message', 'The end time must be in the future and after the start time.');   
            }
            $ballot->end = $end;
            $ballot->save();
            return redirect()->route('ballots.index')->with('message', 'Ballot saved');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ballot  $ballot
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Ballot $ballot)
    {
        if ($request->input('confirm') == $ballot->title) {
            $ballot->options()->delete();
            $ballot->members()->detach();
            $ballot->delete();
            return redirect()->route('ballots.index')->with('message', 'Ballot Deleted');
        } else {
            return back()->with('message', 'Deletion confirmation not given');
        }
    }
}
