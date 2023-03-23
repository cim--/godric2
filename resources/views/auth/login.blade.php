<x-layout>
    <x-slot:title>Log in</x-slot:title>

    @if ($orgtype == "UCUBranch")
	<p>If you cannot remember your membership ID, you can send a blank email to <a href="mailto:mynumber@mercury.ucu.org.uk">mynumber@mercury.ucu.org.uk</a> from the email address we have recorded for you, and your membership ID will be emailed back. You can also get it by logging into <a href="https://www.ucu.org.uk/article/8903/My-UCU">MyUCU</a>.</p>
    @endif
    
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
