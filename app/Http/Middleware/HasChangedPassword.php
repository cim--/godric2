<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class HasChangedPassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('auth.login');
        }
        if (!$user->member) {
            // shouldn't normally get here in this case
            // but can occur in rare situations
            Auth::logout();
            $request->session()->regenerate();
            return redirect()->route('auth.login');
        }
        if (Hash::check($user->member->lastname, $user->password)) {
            return redirect()->route('auth.password');
        }
        
        return $next($request);
    }
}
