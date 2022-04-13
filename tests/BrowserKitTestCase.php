<?php

namespace Tests;

use Laravel\BrowserKitTesting\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;

abstract class BrowserKitTestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseTransactions;
    
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
