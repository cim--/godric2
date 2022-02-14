<x-layout>
    <x-slot:title>Data Access Roles: Edit</x-slot:title>

    @if ($role->id)
	{!! Form::open(['route' => ['roles.update', $role->id], 'method' => 'PUT']) !!}
    @else
	{!! Form::open(['route' => 'roles.store', 'method' => 'POST']) !!}
    @endif

    <div>
	{!! Form::label('member', 'Member ID') !!}
	{!! Form::text('member', $role->member ? $role->member->membership : "") !!}
    </div>
    <div>
	{!! Form::label('role', 'Role') !!}
	{!! Form::select('role', $types, $role->role) !!}
    </div>
    <div>
	{!! Form::label('restrictfield', 'Restrict Field') !!}
	{!! Form::select('restrictfield', $fields, $role->restrictfield) !!}
    </div>
    <div>
	{!! Form::label('restrictvalue', 'Restrict Value') !!}
	{!! Form::text('restrictvalue', $role->restrictvalue) !!}
    </div>

    {!! Form::submit("Edit Role") !!}

    {!!  Form::close() !!}

    @if ($role->id)
	<p>Deletion cannot be undone - though you can recreate the role.</p>
	{!! Form::open(['route' => ['roles.destroy', $role->id], 'method' => 'DELETE']) !!}

	{!! Form::submit("Delete Role") !!}
	
	{!! Form::close() !!}
    @endif
	
</x-layout>
