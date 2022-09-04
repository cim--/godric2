<x-layout>
    <x-slot:title>Campaign Report: {{$campaign->name}}</x-slot:title>

    <h2>Campaign progress</h2>
    {!! $chart->render() !!}
    
    @if ($campaign->end->isPast())
	<p><em>Totals for campaigns which have ended are based on current department sizes and will be somewhat inaccurate.</em></p>
    @endif

    @if ($compares->count() > 0)
	@if ($compare)
	    <p>Comparing with {{ $compare->name }}</p>
	@else
	    <p>No comparison selected</p>
	@endif
	<p>
	    Compare with:
	    @if ($compare)
		<a href="{{ route('campaign.report.view', [$campaign->id]) }}">none</a>
	    @else
		none
	    @endif
	    @foreach ($compares as $c)
		|
		@if ($compare && $c->id == $compare->id)
		    {{ $compare->name }}
		@else
		    <a href="{{ route('campaign.report.compare', [$campaign->id, $c->id]) }}">{{ $c->name }}</a>
		@endif
	    @endforeach
	</p>
    @endif
    
    <h2>Departmental breakdown</h2>
    <table class="datatable" data-paging="false" data-searching="false" data-info="false">
	<thead>
	    <tr>
		<th>Department</th>
		<th>Members</th>
		<th>Target</th>
		<th>Participation</th>
		<th>Participation + Intends</th>
		<th>Participation + Intends + Needs Help</th>
		<th>All Responses</th>
		<th>Participation Percentage</th>
		<th>Contact Percentage</th>
	    </tr>
	</thead>
	<tfoot>
	    <tr>
		<td><strong>TOTAL</strong></td>
		<td><strong>{{ $mcount }}</strong></td>
		<td><strong>{{ $campaign->calctarget ? $campaign->calctarget : ceil($mcount * $campaign->target / 100) }}</strong></td>
		<td><strong>{{ $pcount }}</strong></td>
		<td><strong>{{ $wpcount }}</strong></td>
		<td><strong>{{ $whpcount }}</strong></td>
		<td><strong>{{ $ccount }}</strong></td>
		<td><strong>{{ number_format(100*$pcount/$mcount, 1) }}%</strong></td>
		<td><strong>{{ number_format(100*$ccount/$mcount, 1) }}%</strong></td>
	    </tr>
	</tfoot>
	<tbody>
	    @foreach ($departments as $department => $data)
		<tr>
		    <td>{{ $department }}</td>
		    <td>{{ $data['members'] }}</td>
		    <td>{{ ceil($data['members'] * $campaign->target / 100) }}</td>
		    <td>{{ $data['participants'] }}</td>
		    <td>{{ $data['wparticipants'] }}</td>
		    <td>{{ $data['whparticipants'] }}</td>
		    <td>{{ $data['contacts'] }}</td>
		    <td data-sort="{{$data['participants']/$data['members']}}">{{ number_format(100*$data['participants']/$data['members'], 1) }}%</td>
		    <td data-sort="{{$data['contacts']/$data['members']}}">{{ number_format(100*$data['contacts']/$data['members'], 1) }}%</td>

		</tr>
	    @endforeach
	</tbody>
    </table>

    @if ($deptchart)
	<h2>Departmental comparison</h2>
	{!! str_replace('"@@@', '', 
			str_replace('@@@"', '', $deptchart->render())) !!}
    @endif
	    
</x-layout>
