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

    <h2>Role Types</h2>
    <ul>
	<li><strong>Super-user</strong>: can view all data and make changes to roles, import membership lists, set up campaigns, and bulk import campaign participation. There is no point in restricting this role.</li>
	<li><strong>Representative</strong>: can view detailed member lists and record and view past and current campaign participation. Often would be restricted to specific departments or job types.</li>
	<li><strong>Phonebank</strong>: for people helping with campaign participation. Can view campaign reports and search for basic member information to enter current participation details. Can be restricted to a specific department but often granted unrestricted.</li>
	<li><strong>Reporting View</strong>: for other people who should see the high-level campaign reports. There is no detailed membership access on this role, so the restriction setting isn't used.</li>
    </ul>
    <p>People can be granted multiple roles, which act additively.</p>

    @if ($role->id)
	<h2>Delete Role</h2>
	<p>Deletion cannot be undone - though you can recreate the role.</p>
	{!! Form::open(['route' => ['roles.destroy', $role->id], 'method' => 'DELETE']) !!}

	{!! Form::submit("Delete Role") !!}
	
	{!! Form::close() !!}
    @endif
	
</x-layout>
