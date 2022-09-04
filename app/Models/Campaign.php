<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Campaign extends Model implements Participatory
{
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    /* Ballot. Wording around voting. */
    public const CAMPAIGN_BALLOT = "ballot";
    /* Advance Signup. Does not have 'yes' option. */
    public const CAMPAIGN_SIGNUP = "signup";
    /* Petition. Does not have 'wait' option. */
    public const CAMPAIGN_PETITION = "petition";
    /* Miscellaneous. All options, generic wording */
    public const CAMPAIGN_MISC = "misc";
    
    private $pcache = null;
    
    use HasFactory;

    public function actions()
    {
        return $this->hasMany(Action::class);
    }
    
    public function scopeStarted($q)
    {
        $q->whereDate('start', '<=', Carbon::now())
          ->whereDate('end', '>=', Carbon::now());
    }

    public function scopeEnded($q)
    {
        $q->whereDate('end', '<', Carbon::now());
    }


    public function participation(Member $member)
    {
        if ($this->pcache === null) {
            $this->pcache = [];
            foreach ($this->actions as $action) {
                $this->pcache[$action->member_id] = $action->action;
            }
        }
        
        return $this->pcache[$member->id] ?? "-";
    }

    public static function campaignTypes()
    {
        return [
            self::CAMPAIGN_BALLOT => "Ballot",
            self::CAMPAIGN_SIGNUP => "Advance Signup",
            self::CAMPAIGN_PETITION => "Petition",
            self::CAMPAIGN_MISC => "Other Campaign"
        ];
    }

    public function stateDescriptions($pronoun="I")
    {
        switch ($this->campaigntype) {
        case (self::CAMPAIGN_BALLOT):
            return [
                '-' => '(select answer)',
                'yes' => $pronoun." have voted",
                'wait' => $pronoun." have received a ballot and will return it soon",
                'help' => $pronoun." have not received a ballot",
                'no' => $pronoun." have not voted or prefer not to say, but do not need further reminders"
            ];
        case (self::CAMPAIGN_SIGNUP):
            return [
                '-' => '(select answer)',
                'wait' => $pronoun." will participate in this action",
                'help' => $pronoun." need more information about this action",
                'no' => $pronoun." will not participate in this action"
            ];
        case (self::CAMPAIGN_PETITION):
            // reps and admins can't set 'yes' or edit a 'yes'
            if ($pronoun == "I") {
                return [
                    '-' => '(select answer)',
                    'yes' => $pronoun." sign this petition",
                    'help' => $pronoun." need more information about this petition",
                    'no' => $pronoun." will not sign this petition"
                ];
            } else {
                return [
                    '-' => '(select answer)',
                    //                    'yes' => $pronoun." sign this petition",
                    'help' => $pronoun." need more information about this petition",
                    'no' => $pronoun." will not sign this petition"
                ];
            }
        case (self::CAMPAIGN_MISC):
        default:
            return [
                '-' => '(select answer)',
                'yes' => $pronoun." have participated",
                'wait' => $pronoun." will participate soon",
                'help' => $pronoun." need assistance or more information to participate",
                'no' => $pronoun." will not participate"
            ];
        }
    }

    public function shortDesc()
    {
        return $this->name;
    }


    // for chartjs use
    public function progressDataSet($colour)
    {
        $dataset = [
            'label' => $this->shortDesc(),
            'backgroundColor' => 'transparent',
            'borderColor' => $colour,
            'fill' => false,
            'data' => [],
        ];

        $actions = $this->actions()->where('action', 'yes')->orderBy('created_at')->get();
        $dataset['data'][] = [
            'x' => 0,
            'y' => 0
        ];
        foreach ($actions as $idx => $action) {
            $dataset['data'][] = [
                'x' => $action->created_at->diffInMinutes($this->start)/1440,
                'y' => $idx+1
            ];
        }
        return $dataset;
    }

    public function targetDataSet($colour)
    {
        $dataset = [
            'label' => "Target",
            'backgroundColor' => 'transparent',
            'borderColor' => $colour,
            'fill' => false,
            'data' => [],
        ];

        $dataset['data'][] = [
            'x' => 0,
            'y' => 0
        ];
        $dataset['data'][] = [
            'x' => $this->end->diffInMinutes($this->start)/1440,
            'y' => $this->calctarget
        ];

        return $dataset;
    }

    public function participationByDepartment($dept)
    {
        return $this->actions()
                    ->where('action', 'yes')
                    ->whereHas('member', function ($q) use ($dept) {
                        $q->where('department', $dept);
                    })
                    ->count();
    }
}
