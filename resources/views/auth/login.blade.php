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

    <p>If you have not used this system before, your password will be your last name as recorded in membership data. You will need to set a real password after you log in. If you have used this system before but cannot remember your password, you can <a href="{{ route('auth.reset') }}">reset your password</a>.</p>

    <p>If you believe you should be able to log in but cannot, please contact your organisation and ask them to check the membership data.</p>
    
</x-layout>
