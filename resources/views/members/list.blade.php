<x-layout>
    <x-slot:title>Member List</x-slot:title>

    <p>Click on names to update participation in current campaigns.</p>
    
    <table>
	<thead>
	    <tr>
		<th>Membership ID</th>
		<th>Name</th>
		<th>Email</th>
		<th>Phone</th>
		<th>Department</th>
		<th>Voter?</th>
		<th>Past Campaigns</th>
		@foreach ($campaigns as $campaign)
		    <th>{{ $campaign->name }}</th>
		@endforeach
	    </tr>
	</thead>
	<tbody>
	    @foreach ($members as $member)
		<tr>
		    <td>{{ $member->membership }}</td>
		    <td><a href="{{ route('members.edit', $member->id) }}">{{ $member->firstname }} {{ $member->lastname }}</a></td>
		    <td>{{ $member->email }}</td>
		    <td>{{ $member->mobile }}</td>
		    <td>{{ $member->department }}</td>
		    <td>{{ $member->voter ? "Yes" : "No" }}</td>
		    <td>
			@foreach ($pastcampaigns as $pc) 
			    @if ($pc->participation($member) == "yes")
				<span title="{{ $pc->name }}">&#x2714;</span>
			    @endif
			@endforeach
		    </td>
		    @foreach ($campaigns as $campaign)
			<td>{{ $campaign->participation($member) }}</td>
		    @endforeach
		</tr>
	    @endforeach
	</tbody>
    </table>

    
    
</x-layout>
