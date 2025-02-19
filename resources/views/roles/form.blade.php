<x-layout>
    <x-slot:title>Data Access Roles: Edit</x-slot:title>

    @if ($role->id)
	{!! html()->form('PUT',route('roles.update', $role->id))->open() !!}
    @else
	{!! html()->form('POST',route('roles.store'))->open() !!}
    @endif

    <div>
	{!! html()->label('Member ID','member') !!}
	{!! html()->text('member', $role->member ? $role->member->membership : "") !!}
    </div>
    <div>
	{!! html()->label('Role','role') !!}
	{!! html()->select('role', $types, $role->role) !!}
    </div>
    <div>
	{!! html()->label('Restrict Field','restrictfield') !!}
	{!! html()->select('restrictfield', $fields, $role->restrictfield) !!}
    </div>
    <div>
	{!! html()->label('Restrict Value','restrictvalue') !!}
	{!! html()->text('restrictvalue', $role->restrictvalue) !!}
    </div>

    {!! html()->submit("Edit Role") !!}

    {!!  html()->form()->close() !!}

    <h2>Role Types</h2>
    <ul>
	<li><strong>Super-user</strong>: can view all data and make changes to roles, import membership lists, set up campaigns, and bulk import campaign participation. There is no point in restricting this role.</li>
	<li><strong>Secretary</strong>: can edit notices and information documents. There is no need to restrict this role as it does not grant any member information access.</li>
	<li><strong>Representative</strong>: can view detailed member lists and record and view past and current campaign participation. Often would be restricted to specific departments or job types.</li>
	<li><strong>Campaigner</strong>: same member list permissions as a Representative, but only if there's a campaign currently active. Useful for granting temporary helper permissions.</li>
	<li><strong>Phonebank</strong>: for people helping with campaign participation. Can view campaign reports and search for basic member information to enter current participation details. Can be restricted to a specific department but often granted unrestricted.</li>
	<li><strong>Reporting View</strong>: for other people who should see the high-level campaign reports. There is no need to restrict this role as it does not grant any member information access.</li>
    </ul>
    <p>People can be granted multiple roles, which act additively.</p>

    @if ($role->id)
	<h2>Delete Role</h2>
	<p>Deletion cannot be undone - though you can recreate the role.</p>
	{!! html()->form('DELETE', route('roles.destroy', $role->id))->open() !!}

	{!! html()->submit("Delete Role") !!}

	{!! html()->form()->close() !!}
    @endif

</x-layout>
