<x-layout>
    <x-slot:title>Campaign Report: {{$campaign->name}}</x-slot:title>

    @if ($campaign->end->isPast())
	<p><em>Totals for campaigns which have ended are based on current department sizes and will be somewhat inaccurate.</em></p>
    @endif
    
    <table class="datatable" data-paging="false" data-searching="false" data-info="false">
	<thead>
	    <tr>
		<th>Department</th>
		<th>Members</th>
		<th>Participation</th>
		<th>Target</th>
		<th>Percentage</th>
	    </tr>
	</thead>
	<tfoot>
	    <tr>
		<td><strong>TOTAL</strong></td>
		<td><strong>{{ $mcount }}</strong></td>
		<td><strong>{{ $pcount }}</strong></td>
		<td><strong>{{ $campaign->calctarget ? $campaign->calctarget : ceil($mcount * $campaign->target / 100) }}</strong></td>
		<td><strong>{{ number_format(100*$pcount/$mcount, 1) }}%</strong></td>
	    </tr>
	</tfoot>
	<tbody>
	    @foreach ($departments as $department => $data)
		<tr>
		    <td>{{ $department }}</td>
		    <td>{{ $data['members'] }}</td>
		    <td>{{ $data['participants'] }}</td>
		    <td>{{ ceil($data['members'] * $campaign->target / 100) }}</td>
		    <td data-sort="{{$data['participants']/$data['members']}}">{{ number_format(100*$data['participants']/$data['members'], 1) }}%</td>
		</tr>
	    @endforeach
	</tbody>
    </table>
	    
</x-layout>
