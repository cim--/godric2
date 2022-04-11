<x-layout>
    <x-slot:title>Member Update</x-slot:title>

    <p><strong>Updating for:</strong> {{ $member->firstname }} {{ $member->lastname }}</p>

    <h2>Campaign participation</h2>
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
		    'wait' => 'Intends to participate before deadline',
		    'help' => 'Needs help / ballot not arrived',
		    'no' => 'Will not or can not participate, end contact'
		    ] ,$member->participation($campaign)) !!} 
	    </div>
	@endforeach
	{!! Form::submit("Update campaign participation") !!}
	{!! Form::close() !!}
	
    @endif


    @can('setPassword', $member)
    <h2>Emergency password reset</h2>    
    {!! Form::open(['route'=>['members.setpassword', $member->id], 'method' => 'POST']) !!}
    <p>Where a member is having difficulties with the standard log in or password reset process, due to not receiving the code or having other technical difficulties, you can set a new password here, to tell them by an alternative secure route.</p>

    <p>This option is only available if the member has a user account with the password set to their surname, which will be the case after initial log in or partial use of the password reset process.</p>

    <p>Remember to ask them to change their password to something only they know after they log in.</p>
    
    <div>
	{!! Form::label('newpass', 'New temporary password') !!}
	{!! Form::text('newpass') !!} (minimum 8 characters)
    </div>
    
    {!! Form::submit("Set new password") !!}
    {!! Form::close() !!}

    
    @endcan

	
</x-layout>
