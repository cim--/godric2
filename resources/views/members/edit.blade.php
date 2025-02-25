<x-layout>
    <x-slot:title>Member Update</x-slot:title>

    <p><strong>Updating for:</strong> {{ $member->firstname }} {{ $member->lastname }}</p>

    <h2>Campaign participation</h2>
    @if (count($campaigns) == 0)
	<p>There are currently no active campaigns. Other membership information must be updated nationally if incorrect.</p>
    @else
	<p>Update participation in active campaigns. Other membership information must be updated nationally if incorrect.</p>

	{!! html()->form('POST', route('members.update', $member->id))->open() !!}

	@foreach ($campaigns as $campaign)
	    <x-campaigns.statemenu :campaign="$campaign" :member="$member" />
	@endforeach
	{!! html()->submit("Update campaign participation") !!}
	{!! html()->form()->close() !!}
    @endif


    <h2>Notes</h2>
    {!! html()->form('POST', route('members.updatenotes', $member->id))->open() !!}

    <div>
	{!! html()->label('Additional Notes','notes') !!}
	{!! html()->textarea('notes', $member->notes ) !!}
    </div>

    <p>Record additional notes about a member. Notes will be visible and editable by all representatives. The member will be able to view the notes on their My Profile page.</p>

    {!! html()->submit("Update notes") !!}
    {!! html()->form()->close() !!}

    @if (count($workplaces) > 0)
	<h2>Edit workplace membership</h2>

	<p>Removing workplace membership may require an administrator to reverse, depending on your permissions.</p>

	{!! html()->form('POST',route('members.updateworkplace', $member->id))->open() !!}
	@foreach ($workplaces as $workplace)
	    <div>
        {!! html()->checkbox('workplace'.$workplace->id, 1)->checked(in_array($workplace->id, $member->workplaces->pluck('id')->toArray())) !!}
        {!! html()->label($workplace->name,'workplace'.$workplace->id) !!}
	    </div>
	@endforeach
	{!! html()->submit("Update workplaces") !!}
	{!! html()->form()->close() !!}

    @else
	<h2>Workplace membership</h2>
	<p>Contact your organisation administrators if you would like workplaces setting up.</p>
    @endif


    @can('setPassword', $member)
    <h2>Emergency password reset</h2>
    {!! html()->form('POST',route('members.setpassword', $member->id))->open() !!}
    <p>Where a member is having difficulties with the standard log in or password reset process, due to not receiving the code or having other technical difficulties, you can set a new password here, to tell them by an alternative secure route.</p>

    <p>This option is only available if the member has a user account with the password set to their surname, which will be the case after initial log in or partial use of the password reset process.</p>

    <p>Remember to ask them to change their password to something only they know after they log in.</p>

    <div>
	{!! html()->label('New temporary password','newpass') !!}
	{!! html()->text('newpass') !!} (minimum 8 characters)
    </div>

    {!! html()->submit("Set new password") !!}
    {!! html()->form()->close() !!}


    @endcan


</x-layout>
