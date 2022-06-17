<x-layout>
    <x-slot:title>Log in</x-slot:title>

    <h2>Existing Accounts</h2>
    
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

    <p>If you have used this system before but cannot remember your password, you can <a href="{{ route('auth.reset') }}">reset your password</a>.</p>

    <h2>Create Account</h2>

    {!! Form::open(['method' => 'POST', 'route' => 'auth.dologin']) !!}

    <div>
	{!! Form::label('username', 'Membership ID') !!}
	{!! Form::text('username') !!}
    </div>
    <div>
	{!! Form::label('password', 'Last Name') !!}
	{!! Form::password('password') !!}
	(exactly as in membership records, including punctuation and capitalisation)
    </div>

    {!! Form::submit("Create Account") !!}

    <p>If you believe you should be able to create an account but cannot, please contact your organisation and ask them to check the membership data.</p>

    {!! Form::close() !!}
    
</x-layout>
