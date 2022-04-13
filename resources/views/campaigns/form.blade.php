<x-layout>
    <x-slot:title>Campaigns: Edit</x-slot:title>

    @if ($campaign->id)
	{!! Form::open(['route' => ['campaigns.update', $campaign->id], 'method' => 'PUT']) !!}
    @else
	{!! Form::open(['route' => 'campaigns.store', 'method' => 'POST']) !!}
    @endif

    <div>
	{!! Form::label('name', 'Name') !!}
	{!! Form::text('name', $campaign->name) !!}
    </div>
    <div>
	{!! Form::label('description', 'Description') !!}
	{!! Form::textarea('description', $campaign->description) !!}
    </div>
    <div>
	{!! Form::label('type', 'Campaign Type') !!}
	{!! Form::select('type', App\Models\Campaign::campaignTypes(), $campaign->campaigntype) !!}
    </div>

    <div>
	{!! Form::label('start', 'Start') !!}
	{!! Form::date('start', $campaign->start ? $campaign->start->format("Y-m-d") : "") !!}
    </div>
    <div>
	{!! Form::label('end', 'End') !!}
	{!! Form::date('end', $campaign->end ? $campaign->end->format("Y-m-d") : "") !!}
    </div>
    <div>
	{!! Form::label('target', 'Target') !!}
	{!! Form::number('target', $campaign->target, ['min'=>0, 'max'=>100]) !!}% ({{ $campaign->calctarget ?? "-" }})
    </div>
    <div>
	{!! Form::label('votersonly', 'Voters Only?') !!}
	{!! Form::checkbox('votersonly', 1, $campaign->votersonly) !!}
    </div>

    
    {!! Form::submit("Edit Campaign") !!}

    {!!  Form::close() !!}

    <p>Campaign Type affects the wording and options on the action menus.</p>
    <ul>
	<li><strong>Ballot:</strong> Wording is about receiving and using ballots. Does not automatically cause restriction to voters but usually should.</li>
	<li><strong>Signup:</strong> For advance sign-up to future events. Can't set a "yes" option (you might convert it into a different event type later)</li>
	<li><strong>Petition:</strong> For direct signing of petitions, open letters, etc. Can't set a "wait" option, and admins can't set "yes" or edit a "yes".</li>
	<li><strong>Miscellaneous:</strong> For anything else. Generic wording, all options available.</li>
    </ul>

    
    @if ($campaign->id)
	{!! Form::open(['route' => ['campaigns.destroy', $campaign->id], 'method' => 'DELETE']) !!}
	<p><strong>Warning:</strong> Campaign deletion cannot be undone.</p>
	<div>
	    {!! Form::label('confirm', 'Confirm by typing campaign name') !!}
	    {!! Form::text('confirm') !!}
	</div>
	
	{!! Form::submit("Delete Campaign") !!}

	{!!  Form::close() !!}

    @endif


    
</x-layout>
