<x-layout>
    <x-slot:title>Change Password</x-slot:title>

    {!! Form::open(['method' => 'POST', 'route' => 'auth.password.update']) !!}

    @if ($firsttime)
	<p><strong>As this is the first time you have used this system, you must set a new password, and enter a verification code.</strong> The verification code has been sent to your preferred email address.</p>
    @endif
    
    <div>
	{!! Form::label('cpwd', 'Current Password') !!}
	{!! Form::password('cpwd') !!}
    </div>

    <div>
	{!! Form::label('npwd', 'New Password') !!}
	{!! Form::password('npwd') !!}
    </div>

    <div>
	{!! Form::label('npwd2', 'Confirm New Password') !!}
	{!! Form::password('npwd2') !!}
    </div>

    @if ($firsttime)
	<div>
	    {!! Form::label('code', 'Verification Code') !!}
	    {!! Form::text('code') !!}
	    Check your email for this code - it will be eight letters and numbers
	</div>
    @endif
    
    {!! Form::submit("Change Password") !!}
    
    {!! Form::close() !!}

    <p>Passwords must be at least 8 characters long.</p>
    
</x-layout>
