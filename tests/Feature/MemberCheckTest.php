<?php

namespace Tests\Feature;

use App\Models\ApiToken;
use App\Models\Member;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class MemberCheckTest extends TestCase
{
    private const ENDPOINT = '/api/member/check';

    private string $plaintext;
    private Member $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plaintext = bin2hex(random_bytes(32));
        ApiToken::create([
            'name'  => 'test',
            'token' => hash('sha256', $this->plaintext),
        ]);

        $this->member = Member::whereNotNull('lastname')
            ->where('lastname', '!=', '')
            ->firstOrFail();

        Config::set('api_access.ip_whitelist', ['127.0.0.1']);
    }

    // -------------------------------------------------------------------------
    // Happy path
    // -------------------------------------------------------------------------

    public function test_match_returns_true_for_correct_member_id_and_surname(): void
    {
        $this->authorised()
            ->postJson(self::ENDPOINT, [
                'member_id' => $this->member->membership,
                'surname'   => $this->member->lastname,
            ])
            ->assertOk()
            ->assertExactJson(['match' => true]);
    }

    public function test_match_is_case_insensitive(): void
    {
        $this->authorised()
            ->postJson(self::ENDPOINT, [
                'member_id' => $this->member->membership,
                'surname'   => strtoupper($this->member->lastname),
            ])
            ->assertOk()
            ->assertExactJson(['match' => true]);
    }

    public function test_match_returns_false_for_wrong_surname(): void
    {
        $this->authorised()
            ->postJson(self::ENDPOINT, [
                'member_id' => $this->member->membership,
                'surname'   => 'DEFINITELY_NOT_A_REAL_SURNAME_XYZ',
            ])
            ->assertOk()
            ->assertExactJson(['match' => false]);
    }

    public function test_match_returns_false_for_nonexistent_member_id(): void
    {
        $this->authorised()
            ->postJson(self::ENDPOINT, [
                'member_id' => '99999999',
                'surname'   => 'Smith',
            ])
            ->assertOk()
            ->assertExactJson(['match' => false]);
    }

    // -------------------------------------------------------------------------
    // Token authentication
    // -------------------------------------------------------------------------

    public function test_request_without_authorization_header_is_rejected(): void
    {
        $this->postJson(self::ENDPOINT, [
            'member_id' => $this->member->membership,
            'surname'   => $this->member->lastname,
        ])->assertUnauthorized();
    }

    public function test_request_with_wrong_token_is_rejected(): void
    {
        $this->withToken('not-the-right-token')
            ->postJson(self::ENDPOINT, [
                'member_id' => $this->member->membership,
                'surname'   => $this->member->lastname,
            ])
            ->assertUnauthorized();
    }

    public function test_request_with_malformed_authorization_header_is_rejected(): void
    {
        $this->withHeaders(['Authorization' => 'Basic ' . base64_encode('user:pass')])
            ->postJson(self::ENDPOINT, [
                'member_id' => $this->member->membership,
                'surname'   => $this->member->lastname,
            ])
            ->assertUnauthorized();
    }

    public function test_authorization_header_without_bearer_prefix_is_rejected(): void
    {
        $this->withHeaders(['Authorization' => $this->plaintext])
            ->postJson(self::ENDPOINT, [
                'member_id' => $this->member->membership,
                'surname'   => $this->member->lastname,
            ])
            ->assertUnauthorized();
    }

    public function test_successful_request_updates_last_used_at(): void
    {
        $before = now()->subSecond();

        $this->authorised()->postJson(self::ENDPOINT, [
            'member_id' => $this->member->membership,
            'surname'   => $this->member->lastname,
        ])->assertOk();

        $token = ApiToken::where('token', hash('sha256', $this->plaintext))->first();
        $this->assertNotNull($token->last_used_at);
        $this->assertTrue($token->last_used_at->isAfter($before));
    }

    public function test_token_is_stored_as_hash_not_plaintext(): void
    {
        $this->assertDatabaseMissing('api_tokens', ['token' => $this->plaintext]);
        $this->assertDatabaseHas('api_tokens', ['token' => hash('sha256', $this->plaintext)]);
    }

    public function test_revoked_token_is_rejected(): void
    {
        ApiToken::where('token', hash('sha256', $this->plaintext))->first()->revoke();

        $this->authorised()
            ->postJson(self::ENDPOINT, [
                'member_id' => $this->member->membership,
                'surname'   => $this->member->lastname,
            ])
            ->assertUnauthorized();
    }

    // -------------------------------------------------------------------------
    // IP whitelist
    // -------------------------------------------------------------------------

    public function test_request_from_non_whitelisted_ip_is_rejected(): void
    {
        Config::set('api_access.ip_whitelist', ['10.0.0.1']);

        $this->authorised()
            ->postJson(self::ENDPOINT, [
                'member_id' => $this->member->membership,
                'surname'   => $this->member->lastname,
            ])
            ->assertForbidden();
    }

    public function test_empty_whitelist_blocks_all_requests(): void
    {
        Config::set('api_access.ip_whitelist', []);

        $this->authorised()
            ->postJson(self::ENDPOINT, [
                'member_id' => $this->member->membership,
                'surname'   => $this->member->lastname,
            ])
            ->assertForbidden();
    }

    public function test_request_from_ip_within_whitelisted_cidr_range_is_allowed(): void
    {
        Config::set('api_access.ip_whitelist', ['127.0.0.0/8']);

        $this->authorised()
            ->postJson(self::ENDPOINT, [
                'member_id' => $this->member->membership,
                'surname'   => $this->member->lastname,
            ])
            ->assertOk();
    }

    public function test_request_from_ip_outside_whitelisted_cidr_range_is_rejected(): void
    {
        Config::set('api_access.ip_whitelist', ['10.127.127.0/24']);

        $this->authorised()
            ->postJson(self::ENDPOINT, [
                'member_id' => $this->member->membership,
                'surname'   => $this->member->lastname,
            ])
            ->assertForbidden();
    }

    public function test_ip_check_runs_before_token_check(): void
    {
        // A blocked IP must get 403, not 401, even if no token is supplied —
        // confirming the whitelist is the outermost gate.
        Config::set('api_access.ip_whitelist', ['10.0.0.1']);

        $this->postJson(self::ENDPOINT, [
            'member_id' => $this->member->membership,
            'surname'   => $this->member->lastname,
        ])->assertForbidden();
    }

    // -------------------------------------------------------------------------
    // Input validation
    // -------------------------------------------------------------------------

    public function test_missing_member_id_returns_422(): void
    {
        $this->authorised()
            ->postJson(self::ENDPOINT, ['surname' => 'Smith'])
            ->assertUnprocessable();
    }

    public function test_missing_surname_returns_422(): void
    {
        $this->authorised()
            ->postJson(self::ENDPOINT, ['member_id' => '1234'])
            ->assertUnprocessable();
    }

    public function test_empty_body_returns_422(): void
    {
        $this->authorised()
            ->postJson(self::ENDPOINT, [])
            ->assertUnprocessable();
    }

    public function test_member_id_over_10_chars_returns_422(): void
    {
        $this->authorised()
            ->postJson(self::ENDPOINT, [
                'member_id' => '12345678901',
                'surname'   => 'Smith',
            ])
            ->assertUnprocessable();
    }

    // -------------------------------------------------------------------------
    // HTTP method enforcement
    // -------------------------------------------------------------------------

    public function test_get_request_is_not_allowed(): void
    {
        $this->authorised()
            ->getJson(self::ENDPOINT)
            ->assertMethodNotAllowed();
    }

    public function test_put_request_is_not_allowed(): void
    {
        $this->authorised()
            ->putJson(self::ENDPOINT, [
                'member_id' => $this->member->membership,
                'surname'   => $this->member->lastname,
            ])
            ->assertMethodNotAllowed();
    }

    // -------------------------------------------------------------------------
    // Enumeration resistance
    // -------------------------------------------------------------------------

    public function test_response_shape_is_identical_for_wrong_id_and_wrong_surname(): void
    {
        // Both failure modes must return exactly {"match": false} — no
        // distinguishing information about which field was wrong.
        $wrongId = $this->authorised()->postJson(self::ENDPOINT, [
            'member_id' => '99999999',
            'surname'   => $this->member->lastname,
        ]);

        $wrongSurname = $this->authorised()->postJson(self::ENDPOINT, [
            'member_id' => $this->member->membership,
            'surname'   => 'DEFINITELY_NOT_A_REAL_SURNAME_XYZ',
        ]);

        $wrongId->assertExactJson(['match' => false]);
        $wrongSurname->assertExactJson(['match' => false]);
        $this->assertSame($wrongId->getStatusCode(), $wrongSurname->getStatusCode());
    }

    public function test_failed_auth_leaks_no_member_information(): void
    {
        // A 401 from a bad token must not reveal whether the member_id exists.
        $response = $this->withToken('bad-token')
            ->postJson(self::ENDPOINT, [
                'member_id' => $this->member->membership,
                'surname'   => $this->member->lastname,
            ]);

        $response->assertUnauthorized();
        $body = $response->json();
        $this->assertArrayNotHasKey('match', $body);
        $this->assertArrayNotHasKey('member_id', $body);
    }

    // -------------------------------------------------------------------------
    // Helper
    // -------------------------------------------------------------------------

    private function authorised(): static
    {
        return $this->withToken($this->plaintext);
    }
}
