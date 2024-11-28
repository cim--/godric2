<x-layout>
    <x-slot:title>Ballots: Edit</x-slot:title>

    @if ($ballot->id)
	@if (!$ballot->ended())
	    {!! html()->form('PUT', route('ballots.update', $ballot->id))->open() !!}
	@endif
    @else
	{!! html()->form('POST','ballots.store')->open() !!}
    @endif

    @if ($ballot->started())
	<div><strong>Title: </strong> {{ $ballot->title }}</div>
	<div><strong>Description: </strong> {{ $ballot->description }}</div>
	<div><strong>Voters only: </strong> {{ $ballot->votersonly ? "Yes" : "No" }}</div>
	<div><strong>Options: </strong> {{ $ballot->options->pluck('option')->join("; ") }}</div>
	<div><strong>Start: </strong> {{ $ballot->start->format("j F Y H:i") }}</div>
    @else
    <div>
	{!! html()->label('title', 'Title') !!}
	{!! html()->text('title', $ballot->title) !!}
    </div>
    <div>
	{!! html()->label('description', 'Description') !!}
	{!! html()->textarea('description', $ballot->description, ['class' => 'htmlbox']) !!} (HTML allowed)
    </div>
    <div>
	{!! html()->label('votersonly', 'Voters Only?') !!}
	{!! html()->checkbox('votersonly', 1, $ballot->votersonly) !!}
    </div>
    <div>
	{!! html()->label('options', 'Options') !!}
	{!! html()->textarea('options', $ballot->options->pluck('option')->join("\n")) !!}
	<br>(one per line, recommend two options + abstain)
    </div>
    <div>
	{!! html()->label('start', 'Start') !!}
	{!! html()->datetime('start', ($ballot->start ? $ballot->start : \Carbon\Carbon::parse("+1 week"))->format("Y-m-d H:i:s")) !!}
    </div>
    @endif
    @if ($ballot->ended())
	<div><strong>End: </strong> {{ $ballot->end->format("j F Y H:i") }}</div>
    @else
	<div>
	    {!! html()->label('end', 'End') !!}
	    {!! html()->datetime('end', ($ballot->end ? $ballot->end : \Carbon\Carbon::parse("+2 weeks"))->format("Y-m-d H:i:s")) !!}
	</div>
    @endif

    @if (!$ballot->ended())
	{!! html()->submit("Edit Ballot") !!}

	{!!  html()->form()->close() !!}
    @else
	<h2>Results</h2>
	<ul>
	    @foreach ($ballot->options as $option)
		<li>{{ $option->option }}: {{ $option->votes }}</li>
	    @endforeach
	</ul>
    @endif


    @if ($ballot->id)
	{!! html()->form('DELETE', route('ballots.destroy', $ballot->id)) !!}
	<p><strong>Warning:</strong> Ballot deletion cannot be undone.</p>
	<div>
	    {!! html()->label('confirm', 'Confirm by typing ballot title') !!}
	    {!! html()->text('confirm') !!}
	</div>

	{!! html()->submit("Delete Ballot") !!}

	{!!  html()->form()->close() !!}
    @endif

</x-layout>
