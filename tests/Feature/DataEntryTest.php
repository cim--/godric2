<?php

namespace Tests\Feature;

use Tests\BrowserKitTestCase as TestCase;
use App\Models\User;
use App\Models\Member;
use App\Models\Campaign;
use App\Models\Action;

use Carbon\Carbon;

class DataEntryTest extends TestCase
{

    public function testSetCampaignActionAsSelf()
    {
        $c = Campaign::started()->first();

        $m = Member::where('membership', 1020)->first();
        
        $this->loginAs('1020')
             ->visitRoute('main')
             ->see($c->name)
             ->select('yes', 'participation'.$c->id)
             ->press('Update participation')
             ->see('Your participation has been updated');

        $this->assertEquals('yes', $c->participation($m));

        $this->select('help', 'participation'.$c->id)
             ->press('Update participation')
             ->see('Your participation has been updated');

        $c = Campaign::started()->first();
        $this->assertEquals('help', $c->participation($m));
    }

    public function testSetPetitionCampaignActionAsSelf()
    {
        $c = Campaign::started()->first();
        $c->campaigntype = Campaign::CAMPAIGN_PETITION;
        $c->save();

        $m = Member::where('membership', 1020)->first();
        
        $this->loginAs('1020')
             ->visitRoute('main')
             ->see($c->name)
             ->see("I sign this petition")
             ->select('yes', 'participation'.$c->id)
             ->press('Update participation')
             ->see('Your participation has been updated');

        $this->assertEquals('yes', $c->participation($m));

        $this->select('help', 'participation'.$c->id)
             ->press('Update participation')
             ->see('Your participation has been updated');

        $c = Campaign::started()->first();
        $this->assertEquals('help', $c->participation($m));
    }

    public function testSetCampaignActionViaRep()
    {
        $member1 = Member::where('department', 'Philosophy')->where('voter', true)->first();
        $c = Campaign::started()->first();

        $this->loginAs('1001')
             ->visitRoute('main')
             ->click('Member Lists')
             ->click($member1->firstname." ".$member1->lastname)
             ->see('Campaign Participation')
             ->seeText('Updating for: '.$member1->firstname." ".$member1->lastname)
             ->select('yes', 'action'.$c->id)
             ->press('Update campaign participation')
             ->see('Updated campaign participation')
             ->see('Click on names to update participation');

        $this->assertEquals('yes', $c->participation($member1));
    }

    public function testSetPetitionActionViaRep()
    {
        $c = Campaign::started()->first();
        $c->campaigntype = Campaign::CAMPAIGN_PETITION;
        $c->save();

        $member1 = Member::where('department', 'Philosophy')->whereDoesntHave('actions', function($q) use ($c) {
            $q->where('campaign_id', $c->id);
        })->where('voter', true)->first();

        $this->loginAs('1001')
             ->visitRoute('main')
             ->click('Member Lists')
             ->click($member1->firstname." ".$member1->lastname)
             ->see('Campaign Participation')
             ->seeText('Updating for: '.$member1->firstname." ".$member1->lastname)
             ->select('help', 'action'.$c->id)
             ->see('you cannot record signatures on their behalf')
             ->press('Update campaign participation')
             ->see('Updated campaign participation')
             ->see('Click on names to update participation');

        $this->assertEquals('help', $c->participation($member1));
    }

    public function testReadonlyYesPetitionActionViaRep()
    {
        $c = Campaign::started()->first();
        $c->campaigntype = Campaign::CAMPAIGN_PETITION;
        $c->save();

        $member1 = Member::where('department', 'Philosophy')->whereHas('actions', function($q) use ($c) {
            $q->where('campaign_id', $c->id)
              ->where('action', 'yes');
        })->where('voter', true)->first();

        $this->loginAs('1001')
             ->visitRoute('main')
             ->click('Member Lists')
             ->click($member1->firstname." ".$member1->lastname)
             ->see('Campaign Participation')
             ->seeText('Updating for: '.$member1->firstname." ".$member1->lastname)
             ->see('The member has signed this petition')
             ->dontSee('They sign this petition');

        $this->assertEquals('yes', $c->participation($member1));
    }

    public function testSetCampaignActionViaPhonebank()
    {
        $member1 = Member::where('membership', 1040)->first();
        $c = Campaign::started()->first();

        $this->loginAs('1003')
             ->visitRoute('main')
             ->click('Campaign Participation')
             ->type($member1->membership, 'search')
             ->press('Search')
             ->see($member1->firstname)
             ->select('wait', 'action'.$c->id)
             ->press('Update participation')
             ->see($member1->lastname.' participation updated');

        $this->assertEquals('wait', $c->participation($member1));
    }

    public function testSetPetitionActionViaPhonebank()
    {
        $c = Campaign::started()->first();
        $c->campaigntype = Campaign::CAMPAIGN_PETITION;
        $c->save();

        $member1 = Member::whereDoesntHave('actions', function($q) use ($c) {
            $q->where('campaign_id', $c->id);
        })->where('voter', true)->where('membership', '!=', '1003')->first();

        $this->loginAs('1003')
             ->visitRoute('main')
             ->click('Campaign Participation')
             ->type($member1->membership, 'search')
             ->press('Search')
             ->see($member1->firstname)
             ->select('no', 'action'.$c->id)
             ->press('Update participation')
             ->see($member1->lastname.' participation updated');

        $this->assertEquals('no', $c->participation($member1));
    }

    public function testReadonlyYesPetitionActionViaPhonebank()
    {
        $c = Campaign::started()->first();
        $c->campaigntype = Campaign::CAMPAIGN_PETITION;
        $c->save();

        $member1 = Member::whereHas('actions', function($q) use ($c) {
            $q->where('campaign_id', $c->id)
              ->where('action', 'yes');
        })->where('voter', true)->first();


        $this->loginAs('1003')
             ->visitRoute('main')
             ->click('Campaign Participation')
             ->type($member1->membership, 'search')
             ->press('Search')
             ->see($member1->firstname)
             ->see('The member has signed this petition');

        $this->assertEquals('yes', $c->participation($member1));
    }


}
