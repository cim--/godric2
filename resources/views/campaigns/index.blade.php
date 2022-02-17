<x-layout>
    <x-slot:title>Campaign List</x-slot:title>

    <p><strong>Current membership</strong>: {{ $members }} ({{ $voters }} voters)</p>
    
    <p><a href="{{ route('campaigns.create') }}">New Campaign</a></p>
    <table>
	<thead>
	    <tr>
		<th>Title</th>
		<th>Start</th>
		<th>End</th>
		<th>Target</th>
		<th>Participation</th>
	    </tr>
	</thead>
	<tbody>
	    @foreach ($campaigns as $campaign)
		<tr>
		    <td><a href="{{ route('campaigns.edit', $campaign->id) }}">
			{{ $campaign->name }}
		    </a></td>
		    <td>{{$campaign->start->format("j F Y")}}</td>
		    <td>{{$campaign->end->format("j F Y")}}</td>
		    <td>
			{{ $campaign->target }}%
		    </td>
		    <td>
			<a href="{{ route('campaigns.import', $campaign->id) }}">
			    {{ $campaign->actions_count }} / {{ $campaign->calctarget }}
			</a>
		    </td>
		</tr>
	    @endforeach
	</tbody>
    </table>
	    
</x-layout>
