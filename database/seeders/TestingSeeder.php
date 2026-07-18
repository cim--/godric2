<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Member;
use App\Models\Campaign;
use App\Models\Action;
use App\Models\Role;
use App\Models\Ballot;
use App\Models\Option;

use Carbon\Carbon;

class TestingSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $pastcampaign = Campaign::factory()->create();
        $currentcampaign = Campaign::factory()->current()->create();

        $memberhash = [];

        // Members that will have a Role attached below. Their own `department`
        // must not collide with the departments tests filter members by
        // ('Philosophy', 'Chemistry', 'Library') — otherwise
        // Member::where('department', ...)->first() can return the logged-in
        // actor itself instead of the regular member a test intends to check.
        $roleHolderIds = [1000, 1001, 1002, 1003, 1004, 1005, 1006, 1009];

        for ($i = 1000; $i <= 1100; $i++) {
            if ($i < 1050) {
                $user = User::factory()->create([
                    'username' => $i,
                ]);
            }
            switch ($i) {
                case 1020:
                case 1030:
                    // set specific members
                    $member = Member::factory()->create([
                        'membership' => $i,
                        'department' => 'Philosophy',
                        'voter' => true,
                    ]);
                    break;
                case 1040:
                    $member = Member::factory()->create([
                        'membership' => $i,
                        'voter' => true,
                    ]);
                    break;
                case 1083:
                case 1084:
                    $member = Member::factory()->create([
                        'membership' => $i,
                        'department' => 'Finance',
                        'voter' => true,
                    ]);
                    break;
                default:
                    // randomise, except role holders — see $roleHolderIds above
                    $member = Member::factory()->create(
                        in_array($i, $roleHolderIds)
                            ? ['membership' => $i, 'department' => 'Finance']
                            : ['membership' => $i]
                    );
            }
            if ($member->voter) {
                switch ($member->id) {
                    case 1020:
                        // force no actions
                        break;
                    case 1030:
                        // force yes action this time
                        Action::factory()->create([
                            'member_id' => $member->id,
                            'campaign_id' => $currentcampaign->id,
                            'action' => 'yes',
                            'created_at' => $currentcampaign->start
                                ->copy()
                                ->addDays(rand(1, 6))
                                ->addMinutes(rand(0, 1440)),
                        ]);
                        break;
                    default:
                        if (rand(0, 10) < 5) {
                            Action::factory()->create([
                                'member_id' => $member->id,
                                'campaign_id' => $pastcampaign->id,
                                'created_at' => $pastcampaign->start
                                    ->copy()
                                    ->addDays(rand(1, 28))
                                    ->addMinutes(rand(0, 1440)),
                            ]);
                            if (rand(0, 10) < 3) {
                                Action::factory()->create([
                                    'member_id' => $member->id,
                                    'campaign_id' => $currentcampaign->id,
                                    'created_at' => $currentcampaign->start
                                        ->copy()
                                        ->addDays(rand(1, 6))
                                        ->addMinutes(rand(0, 1440)),
                                ]);
                            }
                        } elseif (rand(0, 10) < 2) {
                            Action::factory()->create([
                                'member_id' => $member->id,
                                'campaign_id' => $currentcampaign->id,
                                'created_at' => $currentcampaign->start
                                    ->copy()
                                    ->addDays(rand(1, 6))
                                    ->addMinutes(rand(0, 1440)),
                            ]);
                        }
                }
            }
            /* Set up roles */
            switch ($i) {
                case 1000:
                    Role::factory()->create([
                        'member_id' => $member->id,
                        'role' => Role::ROLE_SUPERUSER,
                    ]);
                    break;
                case 1001:
                    Role::factory()->create([
                        'member_id' => $member->id,
                        'role' => Role::ROLE_REP,
                        'restrictfield' => 'department',
                        'restrictvalue' => 'Philosophy',
                    ]);
                    break;
                case 1002:
                    Role::factory()->create([
                        'member_id' => $member->id,
                        'role' => Role::ROLE_REP,
                        'restrictfield' => 'department',
                        'restrictvalue' => 'Library',
                    ]);
                    Role::factory()->create([
                        'member_id' => $member->id,
                        'role' => Role::ROLE_PHONEBANK,
                        'restrictfield' => '',
                        'restrictvalue' => '',
                    ]);
                    break;
                case 1003:
                    Role::factory()->create([
                        'member_id' => $member->id,
                        'role' => Role::ROLE_PHONEBANK,
                        'restrictfield' => '',
                        'restrictvalue' => '',
                    ]);
                    break;
                case 1004:
                    Role::factory()->create([
                        'member_id' => $member->id,
                        'role' => Role::ROLE_REP,
                        'restrictfield' => 'jobtype',
                        'restrictvalue' => 'Postgraduate',
                    ]);
                    break;
                case 1005:
                    Role::factory()->create([
                        'member_id' => $member->id,
                        'role' => Role::ROLE_CAMPAIGNER,
                        'restrictfield' => 'department',
                        'restrictvalue' => 'Chemistry',
                    ]);
                    break;
                case 1006:
                    Role::factory()->create([
                        'member_id' => $member->id,
                        'role' => Role::ROLE_PHONEBANK,
                        'restrictfield' => 'department',
                        'restrictvalue' => 'Library',
                    ]);
                    break;
                case 1009:
                    Role::factory()->create([
                        'member_id' => $member->id,
                        'role' => Role::ROLE_SECRETARY,
                        'restrictfield' => '',
                        'restrictvalue' => '',
                    ]);
                    break;
                default:
                // no roles
            }
            $memberhash[$i] = $member->id;
        }

        // Member search matches membership/email/mobile via LIKE '%term%', so
        // tests searching by membership number can get an extra, unrelated
        // match if some other member's randomly-generated mobile or email
        // happens to contain those digits as a substring. Regenerate any
        // field that collides with a real membership number to keep search
        // results deterministic.
        foreach (Member::all() as $member) {
            $changed = false;

            while ($member->mobile !== '' && self::collidesWithMembership($member->mobile)) {
                $prefix = substr($member->mobile, 0, 2);
                $member->mobile = $prefix . fake()->randomNumber(9, true);
                $changed = true;
            }

            while (self::collidesWithMembership($member->email)) {
                $member->email = fake()->unique()->safeEmail();
                $changed = true;
            }

            if ($changed) {
                $member->save();
            }
        }

        $pastcampaign->calctarget = ceil(Member::voter()->count() / 2);
        $pastcampaign->save();
        $currentcampaign->calctarget = ceil(Member::voter()->count() / 2);
        $currentcampaign->save();

        /* Ballots */

        $ballot1 = Ballot::factory()->create([
            'start' => Carbon::parse('-3 months'),
            'end' => Carbon::parse('-10 weeks'),
        ]);
        $options = Option::factory()
            ->count(3)
            ->create([
                'ballot_id' => $ballot1->id,
            ]);
        $total = min($options->sum('votes'), 45);
        for ($i = 1099; $i > 1099 - $total; $i -= 2) {
            $ballot1->members()->attach($memberhash[$i]);
        }

        $ballot2 = Ballot::factory()->create([
            'start' => Carbon::parse('-4 weeks'),
            'end' => Carbon::parse('-3 weeks'),
        ]);
        $options = Option::factory()
            ->count(6)
            ->create([
                'ballot_id' => $ballot2->id,
            ]);
        $total = min($options->sum('votes'), 90);
        for ($i = 1000; $i < 1000 + $total; $i++) {
            $ballot2->members()->attach($memberhash[$i]);
        }

        $ballot3 = Ballot::factory()->create([
            'title' => "Should we accept the employer's offer on workload?",
            'description' => "The employer has put forward an <a href='https://www.example.com/'>offer on workload</a>.".
            " Should we accept the offer and end the local dispute?",
            'start' => Carbon::parse("-1 day"),
            'end' => Carbon::parse("+3 days")
        ]);
        $options = collect([
            Option::factory()->create([
                'ballot_id' => $ballot3->id,
                'option' => 'Yes'
            ]),
            Option::factory()->create([
                'ballot_id' => $ballot3->id,
                'option' => 'No'
            ]),
            Option::factory()->create([
                'ballot_id' => $ballot3->id,
                'option' => 'Abstain'
            ]),
        ]);
        $options = Option::factory()
            ->count(3)
            ->create([
                'ballot_id' => $ballot3->id,
            ]);

        $total = min($options->sum('votes'), 90);
        for ($i = 1099; $i > 1099 - $total; $i--) {
            $ballot3->members()->attach($memberhash[$i]);
        }
    }

    private static function collidesWithMembership(string $value): bool
    {
        for ($m = 1000; $m <= 1100; $m++) {
            if (str_contains($value, (string) $m)) {
                return true;
            }
        }

        return false;
    }
}
