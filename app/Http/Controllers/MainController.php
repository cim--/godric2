<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Campaign;
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

        return view('index', [
            'campaigns' => $list,
            'self' => $self
        ]);
    }
}
