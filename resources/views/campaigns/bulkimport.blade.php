<x-layout>
    <x-slot:title>Campaigns: Import Actions</x-slot:title>

    {!! Form::open(['route' => ['campaigns.import.process', $campaign->id], 'method' => 'POST']) !!}

    <p>Import actions for {{ $campaign->name }}</p>
    
    <div>
	{!! Form::label('action', 'Import as action') !!}
	{!! Form::select('action', [
	    'yes' => 'Participated',
	    'wait' => 'Intends to',
	    'help' => 'Needs help',
	    'no' => 'No, end contact'
	    ] ,'yes') !!}
    </div>
    <div>
	{!! Form::label('members', 'Members') !!}
	{!! Form::textarea('members', '', ['rows'=>20]) !!}
	<br>
	One per line, using membership ID, email or phone
    </div>
    
    {!! Form::submit("Import Actions") !!}

    {!!  Form::close() !!}
    
</x-layout>
