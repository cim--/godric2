<?php

namespace App\Http\Controllers;

use App\Models\Workplace;
use App\Models\Member;
use Illuminate\Http\Request;

class WorkplaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $workplaces = Workplace::withCount('members')->orderBy('name')->get();

        return view('workplace.index', [
            'workplaces' => $workplaces,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('workplace.form', [
            'workplace' => new Workplace(),
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
        return $this->update($request, new Workplace());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Workplace  $workplace
     * @return \Illuminate\Http\Response
     */
    public function edit(Workplace $workplace)
    {
        return view('workplace.form', [
            'workplace' => $workplace,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Workplace  $workplace
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Workplace $workplace)
    {
        $name = $request->input('name');
        if (
            !$name ||
            Workplace::where('name', $name)
                ->where('id', '!=', $workplace->id)
                ->count() > 0
        ) {
            return back()->with('message', 'Workplace name must be unique');
        }

        $workplace->name = $name;
        $workplace->save();

        $message = 'Updated workplace. ';

        $newmembers = $request->input('newmembers');
        if ($newmembers != '') {
            $lines = explode("\n", trim($newmembers));
            foreach ($lines as $line) {
                if (trim($line) != '') {
                    $member = Member::search($line);
                    if ($member) {
                        $workplace->members()->attach($member->id);
                        $message .=
                            'Added ' .
                            $member->firstname .
                            ' ' .
                            $member->lastname .
                            '. ';
                    } else {
                        $message .= trim($line) . ' not recognised. ';
                    }
                }
            }
        }

        $detach = $request->input('detach', []);
        foreach ($detach as $detid) {
            $workplace->members()->detach($detid);
            $message .= 'Removed member. ';
        }

        return redirect()
            ->route('workplaces.edit', $workplace->id)
            ->with('message', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Workplace  $workplace
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Workplace $workplace)
    {
        if ($request->input('confirm') == $workplace->name) {
            $workplace->members()->detach();
            $workplace->delete();
            return redirect()
                ->route('workplaces.index')
                ->with('message', 'Workplace Deleted');
        } else {
            return back()->with('message', 'Deletion confirmation not given');
        }
    }
}
