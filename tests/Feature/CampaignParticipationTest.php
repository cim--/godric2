<?php

namespace Tests\Feature;

use App\Models\Action;
use App\Models\ApiToken;
use App\Models\Campaign;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class CampaignParticipationTest extends TestCase
{
    private string $plaintext;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plaintext = bin2hex(random_bytes(32));
        ApiToken::create([
            'name' => 'test',
            'token' => hash('sha256', $this->plaintext),
        ]);

        Config::set('api_access.ip_whitelist', ['127.0.0.1']);
    }

    private function authorised(): static
    {
        return $this->withToken($this->plaintext);
    }

    // -------------------------------------------------------------------------
    // GET /api/campaigns
    // -------------------------------------------------------------------------

    public function test_campaigns_index_returns_only_active_campaigns(): void
    {
        $active = Campaign::factory()
            ->current()
            ->create(['name' => 'Active One']);
        $ended = Campaign::factory()->create([
            'name' => 'Long Since Ended',
            'start' => Carbon::parse('-6 months'),
            'end' => Carbon::parse('-5 months'),
        ]);
        $future = Campaign::factory()->create([
            'name' => 'Not Started Yet',
            'start' => Carbon::parse('+1 week'),
            'end' => Carbon::parse('+1 month'),
        ]);

        $response = $this->authorised()
            ->getJson('/api/campaigns')
            ->assertOk()
            ->json();

        $ids = array_column($response, 'id');
        $this->assertContains($active->id, $ids);
        $this->assertNotContains($ended->id, $ids);
        $this->assertNotContains($future->id, $ids);

        $names = array_column($response, 'name');
        $this->assertContains('Active One', $names);
    }

    public function test_campaigns_index_requires_token(): void
    {
        $this->getJson('/api/campaigns')->assertUnauthorized();
    }

    public function test_campaigns_index_requires_whitelisted_ip(): void
    {
        Config::set('api_access.ip_whitelist', ['10.0.0.1']);

        $this->authorised()->getJson('/api/campaigns')->assertForbidden();
    }

    // -------------------------------------------------------------------------
    // POST /api/campaigns/{campaign}/participation
    // -------------------------------------------------------------------------

    public function test_updates_participation_for_a_valid_member_and_status(): void
    {
        $campaign = Campaign::factory()->current()->create();
        $member = Member::factory()->create([
            'membership' => '12345',
            'voter' => true,
        ]);

        $this->authorised()
            ->postJson("/api/campaigns/{$campaign->id}/participation", [
                'member_id' => '12345',
                'status' => 'yes',
            ])
            ->assertOk()
            ->assertExactJson(['updated' => true]);

        $this->assertDatabaseHas('actions', [
            'campaign_id' => $campaign->id,
            'member_id' => $member->id,
            'action' => 'yes',
        ]);
    }

    public function test_updates_existing_participation_instead_of_duplicating(): void
    {
        $campaign = Campaign::factory()->current()->create();
        $member = Member::factory()->create([
            'membership' => '12345',
            'voter' => true,
        ]);
        Action::factory()->create([
            'campaign_id' => $campaign->id,
            'member_id' => $member->id,
            'action' => 'wait',
        ]);

        $this->authorised()
            ->postJson("/api/campaigns/{$campaign->id}/participation", [
                'member_id' => '12345',
                'status' => 'yes',
            ])
            ->assertOk();

        $this->assertEquals(
            1,
            Action::where('campaign_id', $campaign->id)->count()
        );
        $this->assertDatabaseHas('actions', [
            'campaign_id' => $campaign->id,
            'member_id' => $member->id,
            'action' => 'yes',
        ]);
    }

    public function test_rejects_status_not_valid_for_the_campaign_type(): void
    {
        $campaign = Campaign::factory()
            ->current()
            ->create(['campaigntype' => Campaign::CAMPAIGN_PETITION]);
        Member::factory()->create(['membership' => '12345', 'voter' => true]);

        // 'wait' is not a valid state for a petition-type campaign
        $this->authorised()
            ->postJson("/api/campaigns/{$campaign->id}/participation", [
                'member_id' => '12345',
                'status' => 'wait',
            ])
            ->assertUnprocessable();

        $this->assertEquals(
            0,
            Action::where('campaign_id', $campaign->id)->count()
        );
    }

    public function test_rejects_nonexistent_member(): void
    {
        $campaign = Campaign::factory()->current()->create();

        $this->authorised()
            ->postJson("/api/campaigns/{$campaign->id}/participation", [
                'member_id' => '99999999',
                'status' => 'yes',
            ])
            ->assertOk()
            ->assertExactJson(['updated' => false]);
    }

    public function test_rejects_update_on_campaign_that_has_not_started(): void
    {
        $campaign = Campaign::factory()->create([
            'start' => Carbon::parse('+1 week'),
            'end' => Carbon::parse('+1 month'),
        ]);
        Member::factory()->create(['membership' => '12345', 'voter' => true]);

        $this->authorised()
            ->postJson("/api/campaigns/{$campaign->id}/participation", [
                'member_id' => '12345',
                'status' => 'yes',
            ])
            ->assertUnprocessable();
    }

    public function test_rejects_update_on_campaign_that_has_ended(): void
    {
        $campaign = Campaign::factory()->create([
            'start' => Carbon::parse('-3 months'),
            'end' => Carbon::parse('-2 months'),
        ]);
        Member::factory()->create(['membership' => '12345', 'voter' => true]);

        $this->authorised()
            ->postJson("/api/campaigns/{$campaign->id}/participation", [
                'member_id' => '12345',
                'status' => 'yes',
            ])
            ->assertUnprocessable();
    }

    public function test_rejects_non_voter_on_votersonly_campaign(): void
    {
        $campaign = Campaign::factory()
            ->current()
            ->create(['votersonly' => true]);
        Member::factory()->create(['membership' => '12345', 'voter' => false]);

        $this->authorised()
            ->postJson("/api/campaigns/{$campaign->id}/participation", [
                'member_id' => '12345',
                'status' => 'yes',
            ])
            ->assertOk()
            ->assertExactJson(['updated' => false]);

        $this->assertEquals(
            0,
            Action::where('campaign_id', $campaign->id)->count()
        );
    }

    public function test_response_shape_is_identical_for_nonexistent_member_and_non_voter(): void
    {
        // Both cases must return exactly {"updated": false} with a 200 --
        // no distinguishing information about which reason caused it, so
        // this endpoint can't be used to probe which membership numbers
        // are real.
        $campaign = Campaign::factory()
            ->current()
            ->create(['votersonly' => true]);
        Member::factory()->create(['membership' => '11111', 'voter' => false]);

        $nonexistent = $this->authorised()->postJson(
            "/api/campaigns/{$campaign->id}/participation",
            ['member_id' => '99999999', 'status' => 'yes']
        );

        $nonVoter = $this->authorised()->postJson(
            "/api/campaigns/{$campaign->id}/participation",
            ['member_id' => '11111', 'status' => 'yes']
        );

        $nonexistent->assertExactJson(['updated' => false]);
        $nonVoter->assertExactJson(['updated' => false]);
        $this->assertSame(
            $nonexistent->getStatusCode(),
            $nonVoter->getStatusCode()
        );
    }

    public function test_member_id_over_10_chars_returns_422(): void
    {
        $campaign = Campaign::factory()->current()->create();

        $this->authorised()
            ->postJson("/api/campaigns/{$campaign->id}/participation", [
                'member_id' => '12345678901',
                'status' => 'yes',
            ])
            ->assertUnprocessable();
    }

    public function test_participation_update_requires_token(): void
    {
        $campaign = Campaign::factory()->current()->create();
        Member::factory()->create(['membership' => '12345', 'voter' => true]);

        $this->postJson("/api/campaigns/{$campaign->id}/participation", [
            'member_id' => '12345',
            'status' => 'yes',
        ])->assertUnauthorized();
    }
}
