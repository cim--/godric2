<x-layout>
    <x-slot:title>Ballots: Edit</x-slot:title>

    @if ($ballot->id)
	@if (!$ballot->ended())
	    {!! Form::open(['route' => ['ballots.update', $ballot->id], 'method' => 'PUT']) !!}
	@endif
    @else
	{!! Form::open(['route' => 'ballots.store', 'method' => 'POST']) !!}
    @endif

    @if ($ballot->started())
	<div><strong>Title: </strong> {{ $ballot->title }}</div>
	<div><strong>Description: </strong> {{ $ballot->description }}</div>
	<div><strong>Voters only: </strong> {{ $ballot->votersonly ? "Yes" : "No" }}</div>
	<div><strong>Options: </strong> {{ $ballot->options->pluck('option')->join("; ") }}</div>
	<div><strong>Start: </strong> {{ $ballot->start->format("j F Y H:i") }}</div>
    @else
    <div>
	{!! Form::label('title', 'Title') !!}
	{!! Form::text('title', $ballot->title) !!}
    </div>
    <div>
	{!! Form::label('description', 'Description') !!}
	{!! Form::textarea('description', $ballot->description) !!} (HTML allowed)
    </div>
    <div>
	{!! Form::label('votersonly', 'Voters Only?') !!}
	{!! Form::checkbox('votersonly', 1, $ballot->votersonly) !!}
    </div>
    <div>
	{!! Form::label('options', 'Options') !!}
	{!! Form::textarea('options', $ballot->options->pluck('option')->join("\n")) !!}
	<br>(one per line, recommend two options + abstain)
    </div>
    <div>
	{!! Form::label('start', 'Start') !!}
	{!! Form::datetime('start', ($ballot->start ? $ballot->start : \Carbon\Carbon::parse("+1 week"))->format("Y-m-d H:i:s")) !!}
    </div>
    @endif
    @if ($ballot->ended())
	<div><strong>End: </strong> {{ $ballot->end->format("j F Y H:i") }}</div>
    @else
	<div>
	    {!! Form::label('end', 'End') !!}
	    {!! Form::datetime('end', ($ballot->end ? $ballot->end : \Carbon\Carbon::parse("+2 weeks"))->format("Y-m-d H:i:s")) !!}
	</div>
    @endif

    @if (!$ballot->ended())
	{!! Form::submit("Edit Ballot") !!}

	{!!  Form::close() !!}
    @else
	<h2>Results</h2>
	<ul>
	    @foreach ($ballot->options as $option)
		<li>{{ $option->option }}: {{ $option->votes }}</li>
	    @endforeach
	</ul>
    @endif

    
    @if ($ballot->id)
	{!! Form::open(['route' => ['ballots.destroy', $ballot->id], 'method' => 'DELETE']) !!}
	<p><strong>Warning:</strong> Ballot deletion cannot be undone.</p>
	<div>
	    {!! Form::label('confirm', 'Confirm by typing ballot title') !!}
	    {!! Form::text('confirm') !!}
	</div>
	
	{!! Form::submit("Delete Ballot") !!}

	{!!  Form::close() !!}
    @endif
    
</x-layout>
