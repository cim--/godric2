<x-layout>
    <x-slot:title>Reset Password</x-slot:title>

    {!! Form::open(['method' => 'POST', 'route' => 'auth.doreset']) !!}

    <p>To reset your password, enter your membership ID, last name and preferred contact email <em>exactly</em> as in the membership records. If this is successful, you will then be able to log in again using your last name as the password and entering a code sent to your email address.</p>
    
    <div>
	{!! Form::label('username', 'Membership ID') !!}
	{!! Form::text('username') !!}
    </div>
    <div>
	{!! Form::label('lastname', 'Last Name') !!}
	{!! Form::password('lastname') !!}
    </div>
    <div>
	{!! Form::label('email', 'Email') !!}
	{!! Form::password('email') !!}
    </div>

    {!! Form::submit("Reset password") !!}
    
    {!! Form::close() !!}

    <p>If you are unable to use this form, please contact your organisation and ask them to reset your password.</p>
    
</x-layout>
