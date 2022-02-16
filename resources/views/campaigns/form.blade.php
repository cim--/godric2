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
    
</x-layout>
