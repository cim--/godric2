<?php

namespace Tests\Feature;

use Tests\BrowserKitTestCase as TestCase;
use App\Models\User;
use App\Models\Member;
use App\Models\Campaign;

use Carbon\Carbon;

class PermissionsTest extends TestCase
{

    public function testSuperuserCanSeeAllMembers()
    {
        $member1 = Member::where('department', 'Philosophy')->first();
        $member2 = Member::where('department', 'Chemistry')->where('mobile', '!=', '')->first();
        
        $this->loginAs('1000')
             ->visitRoute('main')
             ->seeInElement('nav', 'Member Lists')
             ->seeInElement('nav', 'Campaign Participation')
             ->click('Member Lists')
             ->seeElement('table')
             ->seeElementCount('tbody tr', Member::count())
             ->see($member1->membership)
             ->see($member2->membership)
             ->click('Campaign Participation')
             ->type($member1->firstname, 'search')
             ->press('Search')
             ->see($member1->lastname)
             ->type($member2->membership, 'search')
             ->press('Search')
             ->see($member2->firstname)
             ->type($member1->email, 'search')
             ->press('Search')
             ->see($member1->firstname)
             ->type($member2->mobile, 'search')
             ->press('Search')
             ->see($member2->lastname);
    }

    public function testRepCanSeeDepartment()
    {
        $member1 = Member::where('department', 'Philosophy')->first();
        $member2 = Member::where('department', 'Chemistry')->first();
        
        $this->loginAs('1001')
             ->visitRoute('main')
             ->seeInElement('nav', 'Member Lists')
             ->dontSeeInElement('nav', 'Campaign Participation')
             ->click('Member Lists')
             ->seeElement('table')
             ->see($member1->membership)
             ->dontSee($member2->membership);
    }

    public function testRepAndPhonebankerOverlapsCorrectly()
    {
        $member1 = Member::where('department', 'Philosophy')->first();
        $member2 = Member::where('department', 'Library')->first();
        
        $this->loginAs('1002')
             ->visitRoute('main')
             ->seeInElement('nav', 'Member Lists')
             ->seeInElement('nav', 'Campaign Participation')
             ->click('Member Lists')
             ->seeElement('table')
            // can only see their department in detail
             ->dontSee($member1->membership)
             ->see($member2->membership)
             ->click('Campaign Participation')
             ->type($member1->membership, 'search')
             ->press('Search')
            // but can search more broadly here
             ->see($member1->lastname)
             ->type($member2->membership, 'search')
             ->press('Search')
             ->see($member2->firstname);
    }

    public function testPhonebankerAloneCanSeeParticipation()
    {
        $member1 = Member::where('department', 'Philosophy')->first();
        $member2 = Member::where('department', 'Library')->first();
        
        $this->loginAs('1003')
             ->visitRoute('main')
             ->dontSeeInElement('nav', 'Member Lists')
             ->seeInElement('nav', 'Campaign Participation')
             ->click('Campaign Participation')
             ->type($member1->membership, 'search')
             ->press('Search')
             ->see($member1->lastname)
             ->type($member2->membership, 'search')
             ->press('Search')
             ->see($member2->firstname);
    }

    public function testPhonebankerRoleCanBeRestricted()
    {
        $member1 = Member::where('department', 'Chemistry')->where('membership', '!=', 1006)->first();
        $member2 = Member::where('department', 'Library')->where('membership', '!=', 1006)->first();
        
        $this->loginAs('1006')
             ->visitRoute('main')
             ->dontSeeInElement('nav', 'Member Lists')
             ->seeInElement('nav', 'Campaign Participation')
             ->click('Campaign Participation')
             ->type($member1->membership, 'search')
             ->press('Search')
             ->dontSee($member1->lastname)
             ->see('No members found')
             ->type($member2->membership, 'search')
             ->press('Search')
             ->see($member2->firstname);
    }

    public function testCampaignerRoleWorksLikeRep()
    {
        $member1 = Member::where('department', 'Philosophy')->first();
        $member2 = Member::where('department', 'Chemistry')->first();
        
        $this->loginAs('1005')
             ->visitRoute('main')
             ->seeInElement('nav', 'Member Lists')
             ->dontSeeInElement('nav', 'Campaign Participation')
             ->click('Member Lists')
             ->seeElement('table')
             ->dontSee($member1->membership)
             ->see($member2->membership);
    }

    public function testCampaignerRoleDoesntWorkIfNoCampaigns()
    {
        Campaign::started()->update(['end' => Carbon::yesterday()]);
        $member1 = Member::where('department', 'Philosophy')->first();
        $member2 = Member::where('department', 'Chemistry')->first();
        
        $this->loginAs('1005')
             ->visitRoute('main')
             ->dontSeeInElement('nav', 'Member Lists')
             ->dontSeeInElement('nav', 'Campaign Participation');
    }

    public function testPhonebankerRoleDoesntWorkIfNoCampaigns()
    {
        Campaign::started()->update(['end' => Carbon::yesterday()]);
        
        $this->loginAs('1005')
             ->visitRoute('main')
             ->dontSeeInElement('nav', 'Member Lists')
             ->dontSeeInElement('nav', 'Campaign Participation');
    }

}
