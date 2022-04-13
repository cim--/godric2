<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Campaign;

class CampaignTest extends TestCase
{

    private function verifyCampaignKeys($keys, $yes, $wait, $help, $no)
    {
        $this->assertTrue(isset($keys['-']));
        $this->assertEquals($yes, isset($keys['yes']));
        $this->assertEquals($wait, isset($keys['wait']));
        $this->assertEquals($help, isset($keys['help']));
        $this->assertEquals($no, isset($keys['no']));
    }
    
    public function testCampaignKeysBallot()
    {
        $campaign = new Campaign;
        $campaign->campaigntype = Campaign::CAMPAIGN_BALLOT;

        $i = $campaign->stateDescriptions("I");
        $this->verifyCampaignKeys($i, true, true, true, true);
        $they = $campaign->stateDescriptions("They");
        $this->verifyCampaignKeys($they, true, true, true, true);
    }

    public function testCampaignKeysSignup()
    {
        // no yes option
        $campaign = new Campaign;
        $campaign->campaigntype = Campaign::CAMPAIGN_SIGNUP;

        $i = $campaign->stateDescriptions("I");
        $this->verifyCampaignKeys($i, false, true, true, true);
        $they = $campaign->stateDescriptions("They");
        $this->verifyCampaignKeys($they, false, true, true, true);
    }

    public function testCampaignKeysPetition()
    {
        // no wait option, no yes option for third parties
        $campaign = new Campaign;
        $campaign->campaigntype = Campaign::CAMPAIGN_PETITION;

        $i = $campaign->stateDescriptions("I");
        $this->verifyCampaignKeys($i, true, false, true, true);
        $they = $campaign->stateDescriptions("They");
        $this->verifyCampaignKeys($they, false, false, true, true);
    }

    public function testCampaignKeysMisc()
    {
        $campaign = new Campaign;
        $campaign->campaigntype = Campaign::CAMPAIGN_MISC;

        $i = $campaign->stateDescriptions("I");
        $this->verifyCampaignKeys($i, true, true, true, true);
        $they = $campaign->stateDescriptions("They");
        $this->verifyCampaignKeys($they, true, true, true, true);
    }
}
