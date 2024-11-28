<x-layout>
    <x-slot:title>Phonebank Search</x-slot:title>

    <h2>Search</h2>

    {!! html()->form('POST',route('phonebank.search'))->open() !!}
    <div>
	{!! html()->label('search', 'Search members') !!}
	{!! html()->text('search', $search) !!}
	{!! html()->submit('Search') !!}
    </div>
    {!! html()->form()->close() !!}

    @if ($results !== null)
	@if (count($results) == 0)
	    <p>No members found - check your search and try again.</p>
	@else

	    @foreach ($results as $result)
		<h3>{{$result->firstname}} {{$result->lastname}}</h3>
		<p>Department: {{$result->department}}</p>
		<p>Notes: {{$result->notes}}</p>
		@if ($result->created_at->gt($newpoint))
		    <p><strong>New member.</strong></p>
		@endif

		{!! html()->form('POST', route('phonebank.update', $result->id))->open() !!}
		@foreach ($campaigns as $campaign)
		    <x-campaigns.statemenu :campaign="$campaign" :member="$result" />
		@endforeach
		<div>Update Notes:<br>{!! html()->textarea('notes', $result->notes) !!}</div>
		{!! html()->submit('Update participation') !!}
		{!! html()->form()->close() !!}

	    @endforeach

	@endif
    @endif

</x-layout>
