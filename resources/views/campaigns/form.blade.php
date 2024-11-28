<x-layout>
    <x-slot:title>Campaigns: Edit</x-slot:title>

    @if ($campaign->id)
	{!! html()->form('PUT', route('campaigns.update', $campaign->id))->open() !!}
    @else
	{!! html()->form('POST',route('campaigns.store'))->open() !!}
    @endif

    <div>
	{!! html()->label('name', 'Name') !!}
	{!! html()->text('name', $campaign->name) !!}
    </div>
    <div>
	{!! html()->label('description', 'Description') !!}
	{!! html()->textarea('description', $campaign->description) !!}
    </div>
    <div>
	{!! html()->label('type', 'Campaign Type') !!}
	{!! html()->select('type', App\Models\Campaign::campaignTypes(), $campaign->campaigntype) !!}
    </div>

    <div>
	{!! html()->label('start', 'Start') !!}
	{!! html()->date('start', $campaign->start ? $campaign->start->format("Y-m-d") : "") !!}
    </div>
    <div>
	{!! html()->label('end', 'End') !!}
	{!! html()->date('end', $campaign->end ? $campaign->end->format("Y-m-d") : "") !!}
    </div>
    <div>
	{!! html()->label('target', 'Target') !!}
	{!! html()->number('target', $campaign->target, ['min'=>0, 'max'=>100]) !!}% ({{ $campaign->calctarget ?? "-" }})
    </div>
    <div>
	{!! html()->label('votersonly', 'Voters Only?') !!}
	{!! html()->checkbox('votersonly', 1, $campaign->votersonly) !!}
    </div>


    {!! html()->submit("Edit Campaign") !!}

    {!!  html()->form()->close() !!}

    <p>Campaign Type affects the wording and options on the action menus.</p>
    <ul>
	<li><strong>Ballot:</strong> Wording is about receiving and using ballots. Does not automatically cause restriction to voters but usually should.</li>
	<li><strong>Signup:</strong> For advance sign-up to future events. Can't set a "yes" option (you might convert it into a different event type later)</li>
	<li><strong>Petition:</strong> For direct signing of petitions, open letters, etc. Can't set a "wait" option, and admins can't set "yes" or edit a "yes".</li>
	<li><strong>Miscellaneous:</strong> For anything else. Generic wording, all options available.</li>
    </ul>


    @if ($campaign->id)
	{!! html()->form('DELETE', route('campaigns.destroy', $campaign->id))->open() !!}
	<p><strong>Warning:</strong> Campaign deletion cannot be undone.</p>
	<div>
	    {!! html()->label('confirm', 'Confirm by typing campaign name') !!}
	    {!! html()->text('confirm') !!}
	</div>

	{!! html()->submit("Delete Campaign") !!}

	{!!  html()->form()->close() !!}

    @endif



</x-layout>
