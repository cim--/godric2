<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Campaign;
use App\Models\Notice;
use Carbon\Carbon;

class MainController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::whereDate('end', '>=', Carbon::now())->whereDate('start', '<=', Carbon::now())->withCount(['actions' => function ($q) {
            $q->where('action', 'yes');
        }])->orderBy('name');
        $self = \Auth::user()->member;

        if (!$self->voter) {
            $campaigns->where('votersonly', false);
        }
        $list = $campaigns->get();

        $ballots = $self->activeBallots();
        
        $notices = Notice::current()->highlighted()->orderBy('title')->get();
        
        return view('index', [
            'campaigns' => $list,
            'self' => $self,
            'notices' => $notices,
            'ballots' => $ballots
        ]);
    }

    public function profile()
    {
        $self = \Auth::user()->member()->with('roles', 'workplaces', 'actions', 'actions.campaign')->first();

        $reps = $self->representatives();

        return view('profile', [
            'self' => $self,
            'reps' => $reps
        ]);
    }
}
