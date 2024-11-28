<x-layout>
    <x-slot:title>Change Password</x-slot:title>

    {!! html()-form('POST',route('auth.password.update'))->open() !!}

    @if ($firsttime)
	<p><strong>As this is the first time you have used this system, you must set a new password, and enter a verification code.</strong> The verification code has been sent to your preferred email address.</p>

	<div>
	    {!! html()->label('cpwd', 'Last Name') !!}
	    {!! html()->password('cpwd') !!} (again, exactly as recorded in membership data)
	</div>
    @else
	<div>
	    {!! html()->label('cpwd', 'Current Password') !!}
	    {!! html()->password('cpwd') !!}
	</div>

    @endif


    <div>
	{!! html()->label('npwd', 'New Password') !!}
	{!! html()->password('npwd') !!}
    </div>

    <div>
	{!! html()->label('npwd2', 'Confirm New Password') !!}
	{!! html()->password('npwd2') !!}
    </div>

    @if ($firsttime)
	<div>
	    {!! html()->label('code', 'Verification Code') !!}
	    {!! html()->text('code') !!}
	    Check your email for this code - it will be eight letters and numbers
	</div>
    @endif

    {!! html()->submit("Change Password") !!}

    {!! html()->form()->close() !!}

    <p>Passwords must be at least 8 characters long.</p>

</x-layout>
