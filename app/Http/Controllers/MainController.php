<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Campaign;

class MainController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::whereDate('end', '>=', Carbon::now())->whereDate('start', '<=', Carbon::now())->orderBy('name')->get();
        $self = \Auth::user()->member;
        return view('index', [
            'campaigns' => $campaigns,
            'self' => $self
        ]);
    }
}
