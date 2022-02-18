<x-layout>
    <x-slot:title>Campaign Report: {{$campaign->name}}</x-slot:title>

    @if ($campaign->end->isPast())
	<p><em>Totals for campaigns which have ended are based on current department sizes and will be somewhat inaccurate.</em></p>
    @endif
    
    <table>
	<thead>
	    <tr>
		<th>Department</th>
		<th>Members</th>
		<th>Participation</th>
		<th>Target</th>
	    </tr>
	</thead>
	<tfoot>
	    <tr>
		<td><strong>TOTAL</strong></td>
		<td><strong>{{ $mcount }}</strong></td>
		<td><strong>{{ $pcount }}</strong></td>
		<td><strong>{{ $campaign->calctarget ? $campaign->calctarget : ceil($mcount * $campaign->target / 100) }}</strong></td>
	    </tr>
	</tfoot>
	<tbody>
	    @foreach ($departments as $department => $data)
		<tr>
		    <td>{{ $department }}</td>
		    <td>{{ $data['members'] }}</td>
		    <td>{{ $data['participants'] }}</td>
		    <td>{{ ceil($data['members'] * $campaign->target / 100) }}</td>
		</tr>
	    @endforeach
	</tbody>
    </table>
	    
</x-layout>
