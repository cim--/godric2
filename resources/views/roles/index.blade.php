<x-layout>
    <x-slot:title>Data Access Roles</x-slot:title>

    <p><a href="{{ route('roles.create') }}">New Role</a></p>
    <table>
	<thead>
	    <tr>
		<th>Member</th>
		<th>Role</th>
		<th>Restriction</th>
	    </tr>
	</thead>
	<tbody>
	    @foreach ($roles as $role)
		<tr>
		    <td><a href="{{ route('roles.edit', $role->id) }}">
			{{$role->member->membership}} : {{ $role->member->firstname }} {{ $role->member->lastname }}
		    </a></td>
		    <td>{{$role->role}}</td>
		    <td>
			@if (!$role->restrictfield)
			    "All members"
			@else
			    {{ $role->restrictfield }} = {{ $role->restrictvalue }}
			@endif
		    </td>			
		</tr>
	    @endforeach
	</tbody>
    </table>
	    
</x-layout>
