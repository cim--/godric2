<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NoticeController extends Controller
{

    // public routes
    
    /**
     * Display a listing of the resource for user view
     *
     * @return \Illuminate\Http\Response
     */
    public function publicIndex()
    {
        $notices = Notice::current()->orderBy('highlight', 'desc')->orderBy('start', 'desc')->orderBy('title')->get();
        return view('notices.public', [
            'notices' => $notices
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Notice  $notice
     * @return \Illuminate\Http\Response
     */
    public function read(Notice $notice)
    {
        // exclude notices outside active period
        if (!$notice->isCurrent()) {
            abort(404);
        }
        
        return view('notices.read', [
            'notice' => $notice
        ]);
    }


    // editing routes
    
    /**
     * Display a listing of the resource for editors
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notices = Notice::orderBy('highlight', 'desc')->orderBy('start', 'desc')->orderBy('title')->get();
        return view('notices.index', [
            'notices' => $notices
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('notices.form', [
            'notice' => new Notice
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
        return $this->update($request, new Notice);
    }

    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Notice  $notice
     * @return \Illuminate\Http\Response
     */
    public function edit(Notice $notice)
    {
        return view('notices.form', [
            'notice' => $notice
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Notice  $notice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Notice $notice)
    {
        $title = $request->input('title');
        $content = $request->input('content');
        $start = $request->input('nostart', false) ? null : Carbon::parse($request->input('start'));
        $end = $request->input('noend', false) ? null : Carbon::parse($request->input('end'));
        $highlight = $request->input('highlight', false);

        if (!$title || !$content) {
            dd($title, $content);
            return back()->with('message', 'Title and content are required');
        }
        if ($start && $end && $start->gt($end)) {
            return back()->with('message', 'Start date must be before the end date');
        }
        $notice->title = $title;
        $notice->content = $content;
        $notice->start = $start;
        $notice->end = $end;
        $notice->highlight = $highlight;
        $notice->save();
        return redirect()->route('notices.index')->with('message', 'Notice Saved');
                           
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Notice  $notice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notice $notice)
    {
        $notice->delete();
        return redirect()->route('notices.index')->with('message', 'Notice Removed');
    }
}
