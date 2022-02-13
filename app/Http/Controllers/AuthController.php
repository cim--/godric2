<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function login()
    {
        return view('auth.login');
    }

    public function doLogin(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        $user = User::where('username', $username)->first();
        if (!$user) {
            $member = Member::where('membership', $username)->first();
            if (!$member) {
                return back()->with('message', 'Invalid username or password');
            }
            // member exists, user doesn't, create user record if 'password' okay
            if ($member->lastname == $password) {
                $user = new User;
                $user->username = $username;
                $user->password = Hash::make($member->lastname);
                $user->save();
                
                Auth::login($user);
                $request->session()->regenerate();
                return redirect()->route('auth.password')->with('message', 'Please set your password');
            }
        } else {
            if (!$user->member) {
                // only users with a current member record are allowed
                return back()->with('message', 'Invalid username or password');
            } else {
                
                if (Auth::attempt(['username' => $username, 'password' => $password])) {
                    $request->session()->regenerate();
 
                    return redirect()->intended(route('main'));
                }

                return back()->with('message', 'Invalid username or password');
            }
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->regenerate();
        return redirect()->route('auth.login')->with('message', 'You have logged out');
    }


    public function changePassword()
    {
        return view('auth.password');
    }

    public function updatePassword(Request $request)
    {
        $cpwd = $request->input('cpwd');
        $npwd = $request->input('npwd');
        $npwd2 = $request->input('npwd2');
        if ($npwd != $npwd2) {
            return back()->with('message', 'New password and confirmation do not match');
        }
        if (strlen($npwd) < 8) {
            return back()->with('message', 'Passwords must be at least 8 characters');
        }

        if (!Hash::check($cpwd, $request->user()->password)) {
            return back()->with('message', 'Current password is not correct');
        }

        $user = $request->user();
        $user->password = Hash::make($npwd);
        $user->save();

        return redirect()->route('main')->with('message', 'Password updated');
        
    }
    
}
