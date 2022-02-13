<x-layout>
    <x-slot:title>Log in</x-slot:title>

    {!! Form::open(['method' => 'POST', 'route' => 'auth.dologin']) !!}

    <div>
	{!! Form::label('username', 'Membership ID') !!}
	{!! Form::text('username') !!}
    </div>
    <div>
	{!! Form::label('password', 'Password') !!}
	{!! Form::password('password') !!}
    </div>

    {!! Form::submit("Log in") !!}
    
    {!! Form::close() !!}

    <p>If you have not used this system before, your password will be your last name as recorded in membership data. You will need to set a real password after you log in.</p>
    
</x-layout>
