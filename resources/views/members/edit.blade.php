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
	    <x-campaigns.statemenu :campaign="$campaign" :member="$member" />
	@endforeach
	{!! Form::submit("Update campaign participation") !!}
	{!! Form::close() !!}
    @endif


    <h2>Notes</h2>
    {!! Form::open(['route'=>['members.updatenotes', $member->id], 'method' => 'POST']) !!}
    
    <div>
	{!! Form::label('notes', 'Additional Notes') !!}
	{!! Form::textarea('notes', '') !!} 
    </div>

    <p>Record additional notes about a member. Notes will be visible and editable by all representatives, and of course may be shown to the member themselves.</p>
    
    {!! Form::submit("Update notes") !!}
    {!! Form::close() !!}
    
    @if (count($workplaces) > 0)
	<h2>Edit workplace membership</h2>

	<p>Removing workplace membership may require an administrator to reverse, depending on your permissions.</p>
	
	{!! Form::open(['route'=>['members.updateworkplace', $member->id], 'method' => 'POST']) !!}
	@foreach ($workplaces as $workplace) 
	    <div>
		{!! Form::checkbox('workplace'.$workplace->id, 1, $member->workplaces->where('id', $workplace->id)->count() > 0) !!}
		{!! Form::label('workplace'.$workplace->id, $workplace->name) !!}
	    </div>
	@endforeach
	{!! Form::submit("Update workplaces") !!}
	{!! Form::close() !!}
	
    @else
	<h2>Workplace membership</h2>
	<p>Contact your organisation administrators if you would like workplaces setting up.</p>
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
