<?php

namespace App\Console\Commands;

use App\Models\ApiToken;
use Illuminate\Console\Command;

class RevokeApiToken extends Command
{
    protected $signature = 'api:token:revoke {name : The label of the token to revoke}';
    protected $description = 'Revoke an API token for the member-check endpoint';

    public function handle(): int
    {
        $token = ApiToken::where('name', $this->argument('name'))
            ->whereNull('revoked_at')
            ->first();

        if (!$token) {
            $this->error('No active token found with that name.');
            return self::FAILURE;
        }

        $token->revoke();

        $this->info('Token revoked.');

        return self::SUCCESS;
    }
}
