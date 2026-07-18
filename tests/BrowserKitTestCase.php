<?php

namespace Tests;

use Laravel\BrowserKitTesting\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Database\Seeders\TestingSeeder;

abstract class BrowserKitTestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected $seeder = TestingSeeder::class;

    public $baseUrl = 'http://localhost';

    public function loginAs($username)
    {
        $user = User::where('username', $username)->first();
        if (!$user) {
            $this->assertTrue(false, "User ".$username." exists");
        } else {
            return $this->actingAs($user);
        }
    }
    // ...
}
