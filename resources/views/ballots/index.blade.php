<x-layout>
    <x-slot:title>Ballot List</x-slot:title>

    <p><strong>Current membership</strong>: {{ $members }} ({{ $voters }} voters)</p>
    
    <p><a href="{{ route('ballots.create') }}">New Ballot</a></p>
    <table>
	<thead>
	    <tr>
		<th>Title</th>
		<th>Start</th>
		<th>End</th>
		<th>Participation</th>
	    </tr>
	</thead>
	<tbody>
	    @foreach ($ballots as $ballot)
		<tr>
		    <td><a href="{{ route('ballots.edit', $ballot->id) }}">
			{{ $ballot->title }}
		    </a></td>
		    <td>{{$ballot->start->format("j F Y H:i")}}</td>
		    <td>{{$ballot->end->format("j F Y H:i")}}</td>
		    <td>
			{{ $ballot->options->sum('votes') }}
		    </td>
		</tr>
	    @endforeach
	</tbody>
    </table>


    
</x-layout>
