<x-layout>
    <x-slot:title>Phonebank Search</x-slot:title>

    <h2>Search</h2>

    {!! Form::open(['method'=>'POST', 'route'=>'phonebank.search']) !!}
    <div>
	{!! Form::label('search', 'Search members') !!}
	{!! Form::text('search', $search) !!}
	{!! Form::submit('Search') !!}
    </div>
    {!! Form::close() !!}

    @if ($results !== null)
	@if (count($results) == 0)
	    <p>No members found - check your search and try again.</p>
	@else
	    
	    @foreach ($results as $result)
		<h3>{{$result->firstname}} {{$result->lastname}}</h3>
		<p>Department: {{$result->department}}</p>

		{!! Form::open(['method'=>'post', 'route' => ['phonebank.update', $result->id]]) !!}
		@foreach ($campaigns as $campaign)
		    <x-campaigns.statemenu :campaign="$campaign" :member="$result" />
		@endforeach
		{!! Form::submit('Update participation') !!}
		{!! Form::close() !!}
		
	    @endforeach
		
	@endif
    @endif
    
</x-layout>
