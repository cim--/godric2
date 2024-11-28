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
        $campaigns = Campaign::withCount([
            'actions' => function ($q) {
                $q->where('action', 'yes');
            },
        ])
            ->orderBy('end', 'DESC')
            ->get();
        $membercount = Member::count();
        $votercount = Member::where('voter', true)->count();
        return view('campaigns.index', [
            'campaigns' => $campaigns,
            'members' => $membercount,
            'voters' => $votercount,
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
            'campaign' => new Campaign(),
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
        return $this->update($request, new Campaign());
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
            'campaign' => $campaign,
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
        $campaign->campaigntype = $request->input('type');
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
            $campaign->calctarget = ceil(($campaign->target * $count) / 100);
        } elseif (!$campaign->calctarget) {
            $campaign->calctarget = 0;
        }
        $campaign->save();

        return redirect()
            ->route('campaigns.index')
            ->with('message', 'Campaign edited');
    }

    public function participate(Request $request, Campaign $campaign)
    {
        $part = $request->input('participation' . $campaign->id, '-');

        $self = \Auth::user()->member;
        if (
            $campaign->start->isFuture() ||
            $campaign->end->copy()->addDay()->isPast()
        ) {
            return back()->with(
                'message',
                'This campaign is not currently active'
            );
        }
        if ($campaign->votersonly && !$self->voter) {
            return back()->with('message', 'This campaign is for voters only');
        }

        if ($part == '-') {
            return back()->with('message', 'You have not selected an option');
        }

        $action = Action::firstOrNew([
            'campaign_id' => $campaign->id,
            'member_id' => $self->id,
        ]);
        $action->action = $part;
        $action->save();

        return back()->with('message', 'Your participation has been updated');
    }

    public function bulkImport(Campaign $campaign)
    {
        return view('campaigns.bulkimport', [
            'campaign' => $campaign,
        ]);
    }

    public function bulkImportProcess(Campaign $campaign, Request $request)
    {
        $part = $request->input('action');
        $members = explode("\n", $request->input('members'));
        $notfound = [];
        foreach ($members as $memberid) {
            $memberid = trim($memberid);

            if ($memberid == '') {
                continue;
            }

            $member = Member::search($memberid);
            if ($member) {
                $action = Action::firstOrNew([
                    'campaign_id' => $campaign->id,
                    'member_id' => $member->id,
                ]);
                $action->action = $part;
                $action->save();
            } else {
                $notfound[] = $memberid;
            }
        }

        if (count($notfound) == 0) {
            return redirect()
                ->route('campaigns.index')
                ->with('message', 'Imported actions');
        } else {
            return redirect()
                ->route('campaigns.index')
                ->with(
                    'message',
                    'Imported actions. Some IDs not found: ' .
                        join(',', $notfound)
                );
        }
    }

    public function reportIndex()
    {
        $campaigns = Campaign::withCount([
            'actions' => function ($q) {
                $q->where('action', 'yes');
            },
        ])
            ->orderBy('end', 'DESC')
            ->get();
        return view('campaigns.report.index', [
            'campaigns' => $campaigns,
        ]);
    }

    public function reportView(Campaign $campaign)
    {
        return $this->doReportView($campaign, null);
    }

    public function reportViewCompare(Campaign $campaign, Campaign $compare)
    {
        return $this->doReportView($campaign, $compare);
    }

    protected function doReportView(Campaign $campaign, ?Campaign $compare)
    {
        $departments = [];
        if ($campaign->votersonly) {
            $members = Member::where('voter', true)->get();
        } else {
            $members = Member::all();
        }

        $mcount = 0;
        $pcount = 0;
        $wpcount = 0;
        $whpcount = 0;
        $ccount = 0;
        foreach ($members as $member) {
            $dept = $member->department;
            if (!isset($departments[$dept])) {
                $departments[$dept] = [
                    'members' => 0,
                    'participants' => 0,
                    'wparticipants' => 0,
                    'whparticipants' => 0,
                    'contacts' => 0,
                ];
            }
            $departments[$dept]['members']++;
            $mcount++;
            $participation = $campaign->participation($member);
            switch ($participation) {
                // fall-through is intended!
                case 'yes':
                    $departments[$dept]['participants']++;
                    $pcount++;
                case 'wait':
                    $departments[$dept]['wparticipants']++;
                    $wpcount++;
                case 'help':
                    $departments[$dept]['whparticipants']++;
                    $whpcount++;
                case 'no':
                    $departments[$dept]['contacts']++;
                    $ccount++;
                default:
            }
        }
        ksort($departments);

        // get actions, order by time, cumulative count
        // second series, two points from origin to target-end
        // third series, comparison campaign

        $datasets = [
            'current' => $campaign->progressDataSet('#310c58'),
            'target' => $campaign->targetDataSet('#009000'),
        ];
        if ($compare) {
            $datasets['compare'] = $compare->progressDataSet('#e74898');
        }
        sort($datasets); // compact

        $chart = app()
            ->chartjs->name('progresschart')
            ->type('line')
            ->size(['height' => 300, 'width' => 750])
            ->options([
                'scales' => [
                    'x' => [
                        'type' => 'linear',
                        'position' => 'bottom',
                        'title' => [
                            'display' => true,
                            'text' => 'Day of campaign',
                        ],
                    ],
                    'y' => [
                        'position' => 'left',
                        'title' => [
                            'display' => true,
                            'text' => 'Participants so far',
                        ],
                    ],
                ],
            ])
            ->datasets($datasets);

        $compares = Campaign::where('id', '!=', $campaign->id)
            ->orderBy('start')
            ->get();

        if ($compare) {
            $deptsets = [];
            $deptlist = [];
            $otherlast = 0;
            $otherthis = 0;
            $othertotal = 0;
            $allthis = 0;
            $alltotal = 0;
            foreach ($departments as $department => $data) {
                if ($data['members'] >= 10) {
                    $deptlist[] = [
                        'x' =>
                            (100 *
                                $compare->participationByDepartment(
                                    $department
                                )) /
                            $data['members'],
                        'y' => (100 * $data['participants']) / $data['members'],
                        'label' => $department,
                    ];
                } else {
                    $otherlast += $compare->participationByDepartment(
                        $department
                    );
                    $otherthis += $data['participants'];
                    $othertotal += $data['members'];
                }
                $allthis += $data['participants'];
                $alltotal += $data['members'];
            }
            if ($othertotal > 0) {
                $deptlist[] = [
                    'x' => (100 * $otherlast) / $othertotal,
                    'y' => (100 * $otherthis) / $othertotal,
                    'label' => 'Others',
                ];
            }
            $allmean = (100 * $allthis) / $alltotal;

            $deptsets = [
                [
                    'label' => 'Departments',
                    'data' => $deptlist,
                    'borderColor' => '#310c58',
                    'backgroundColor' => '#310c58',
                    'tooltip' => [
                        'callbacks' => [
                            'label' =>
                                '@@@function(context) { return context.raw.label; }@@@',
                        ],
                    ],
                    'datalabels' => [
                        'display' => true,
                        'align' => 'right',
                    ],
                ],
                [
                    'label' => 'Overall Target',
                    'data' => [
                        [
                            'x' => 0,
                            'y' => $campaign->target,
                        ],
                        [
                            'x' => 100,
                            'y' => $campaign->target,
                        ],
                    ],
                    'borderColor' => '#009000',
                    'backgroundColor' => 'transparent',
                    'showLine' => true,
                ],
                [
                    'label' => 'Average Participation',
                    'data' => [
                        [
                            'x' => 0,
                            'y' => $allmean,
                        ],
                        [
                            'x' => 100,
                            'y' => $allmean,
                        ],
                    ],
                    'borderColor' => '#666666',
                    'backgroundColor' => 'transparent',
                    'showLine' => true,
                ],
                [
                    'label' => 'Improvement',
                    'data' => [
                        [
                            'x' => 0,
                            'y' => 0,
                        ],
                        [
                            'x' => 100,
                            'y' => 100,
                        ],
                    ],
                    'borderColor' => '#e74898',
                    'backgroundColor' => 'transparent',
                    'showLine' => true,
                ],
            ];

            if ($campaign->end->isFuture() && $campaign->start->isPast()) {
                $fraction =
                    Carbon::now()->diffInMinutes($campaign->start) /
                    $campaign->end->diffInMinutes($campaign->start);

                $deptsets[] = [
                    'label' => 'Required Pace for Target',
                    'data' => [
                        [
                            'x' => 0,
                            'y' => $campaign->target * $fraction,
                        ],
                        [
                            'x' => 100,
                            'y' => $campaign->target * $fraction,
                        ],
                    ],
                    'borderColor' => '#009000',
                    'backgroundColor' => 'transparent',
                    'showLine' => true,
                    'borderDash' => [5, 5],
                ];

                $deptsets[] = [
                    'label' => 'Required Pace for Improvement',
                    'data' => [
                        [
                            'x' => 0,
                            'y' => 0,
                        ],
                        [
                            'x' => 100,
                            'y' => 100 * $fraction,
                        ],
                    ],
                    'borderColor' => '#e74898',
                    'backgroundColor' => 'transparent',
                    'showLine' => true,
                    'borderDash' => [5, 5],
                ];
            }

            $deptchart = app()
                ->chartjs->name('departmentchart')
                ->type('scatter')
                ->size(['height' => 300, 'width' => 750])
                ->options([
                    'scales' => [
                        'x' => [
                            'type' => 'linear',
                            'position' => 'bottom',
                            'title' => [
                                'display' => true,
                                'text' => 'Participation last time (%)',
                            ],
                            'min' => 0,
                            'max' => 100,
                        ],
                        'y' => [
                            'position' => 'left',
                            'title' => [
                                'display' => true,
                                'text' => 'Participation this time (%)',
                            ],
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                ])
                ->datasets($deptsets);
        } else {
            $deptchart = null;
        }

        return view('campaigns.report.show', [
            'campaign' => $campaign,
            'departments' => $departments,
            'mcount' => $mcount,
            'pcount' => $pcount,
            'wpcount' => $wpcount,
            'whpcount' => $whpcount,
            'ccount' => $ccount,
            'chart' => $chart,
            'deptchart' => $deptchart,
            'compare' => $compare,
            'compares' => $compares,
        ]);
    }

    public function destroy(Request $request, Campaign $campaign)
    {
        if ($request->input('confirm') == $campaign->name) {
            $campaign->actions()->delete();
            $campaign->delete();
            return redirect()
                ->route('campaigns.index')
                ->with('message', 'Campaign Deleted');
        } else {
            return back()->with('message', 'Deletion confirmation not given');
        }
    }
}
