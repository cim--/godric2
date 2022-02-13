<x-layout>
    <x-slot:title>Change Password</x-slot:title>

    {!! Form::open(['method' => 'POST', 'route' => 'auth.password.update']) !!}

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

    {!! Form::submit("Change Password") !!}
    
    {!! Form::close() !!}

    <p>Passwords must be at least 8 characters long.</p>
    
</x-layout>
