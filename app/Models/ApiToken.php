<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiToken extends Model
{
    protected $fillable = ['name', 'token', 'last_used_at', 'revoked_at'];

    protected $casts = [
        'last_used_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public static function findByPlaintext(string $plaintext): ?self
    {
        return self::where('token', hash('sha256', $plaintext))
            ->whereNull('revoked_at')
            ->first();
    }

    public function revoke(): void
    {
        $this->update(['revoked_at' => now()]);
    }
}
