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
	{!! html()->label('Title','title') !!}
	{!! html()->text('title', $ballot->title) !!}
    </div>
    <div>
	{!! html()->label('Description','description') !!}
	{!! html()->textarea('description', $ballot->description, ['class' => 'htmlbox']) !!} (HTML allowed)
    </div>
    <div>
	{!! html()->label('Voters Only?','votersonly') !!}
	{!! html()->checkbox('votersonly', 1, $ballot->votersonly) !!}
    </div>
    <div>
	{!! html()->label('Options','options') !!}
	{!! html()->textarea('options', $ballot->options->pluck('option')->join("\n")) !!}
	<br>(one per line, recommend two options + abstain)
    </div>
    <div>
	{!! html()->label('Start','start') !!}
	{!! html()->datetime('start', ($ballot->start ? $ballot->start : \Carbon\Carbon::parse("+1 week"))->format("Y-m-d H:i:s")) !!}
    </div>
    @endif
    @if ($ballot->ended())
	<div><strong>End: </strong> {{ $ballot->end->format("j F Y H:i") }}</div>
    @else
	<div>
	    {!! html()->label('End','end') !!}
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
	    {!! html()->label('Confirm by typing ballot title','confirm') !!}
	    {!! html()->text('confirm') !!}
	</div>

	{!! html()->submit("Delete Ballot") !!}

	{!!  html()->form()->close() !!}
    @endif

</x-layout>
