<x-layout>
    <x-slot:title>Workplace List</x-slot:title>

    <p>Workplaces can be used to organise either sub-departmental or non-departmental groups. These can then be used to grant permissions.</p>
    
    <p><a href="{{ route('workplaces.create') }}">New Workplace</a></p>
    <table>
	<thead>
	    <tr>
		<th>Name</th>
		<th>Members</th>
	    </tr>
	</thead>
	<tbody>
	    @foreach ($workplaces as $workplace)
		<tr>
		    <td><a href="{{ route('workplaces.edit', $workplace->id) }}">
			{{ $workplace->name }}
		    </a></td>
		    <td>{{number_format($workplace->members_count)}}</td>
		</tr>
	    @endforeach
	</tbody>
    </table>
	    
</x-layout>
