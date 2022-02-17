<x-layout>
    <x-slot:title>Member Update</x-slot:title>

    <p><strong>Updating for:</strong> {{ $member->firstname }} {{ $member->lastname }}</p>

    @if (count($campaigns) == 0)
	<p>There are currently no active campaigns. Other membership information must be updated nationally if incorrect.</p>
    @else
	<p>Update participation in active campaigns. Other membership information must be updated nationally if incorrect.</p>
	
	{!! Form::open(['route'=>['members.update', $member->id], 'method' => 'POST']) !!}
	
	@foreach ($campaigns as $campaign)
	    <div>
		{!! Form::label('action'.$campaign->id, $campaign->name) !!}
		{!! Form::select('action'.$campaign->id, [
		    '-' => 'Not known',
		    'yes' => 'Participated',
		    'wait' => 'Intends to',
		    'help' => 'Needs help',
		    'no' => 'No, end contact'
		    ] ,$member->participation($campaign)) !!} 
	    </div>
	@endforeach
	{!! Form::submit("Update campaign participation") !!}
	{!! Form::close() !!}
	
    @endif
    
    
    
</x-layout>
