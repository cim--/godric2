<x-layout>
    <x-slot:title>Campaign Reports</x-slot:title>
 
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
		    <td><a href="{{ route('campaign.report.view', $campaign->id) }}">
			{{ $campaign->name }}
		    </a></td>
		    <td>{{$campaign->start->format("j F Y")}}</td>
		    <td>{{$campaign->end->format("j F Y")}}</td>
		    <td>
			{{ $campaign->target }}%
			@if ($campaign->votersonly)
			    (voters)
			@endif
		    </td>
		    <td>
			{{ $campaign->actions_count }} / {{ $campaign->calctarget }}
		    </td>
		</tr>
	    @endforeach
	</tbody>
    </table>
	    
</x-layout>
