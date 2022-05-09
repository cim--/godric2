<x-layout>
    <x-slot:title>Welcome to GODRIC</x-slot:title>

    @if ($notices->count() > 0)
	<ul>
	    @foreach ($notices as $notice)
		<li class="highnotice">
		    <a href="{{ route('notices.read', $notice->id) }}">{{ $notice->title }}</a>
		</li>
	    @endforeach
	</ul>
    @endif
    
    <p>Welcome to the GODRIC system. You can record your participation in campaigns and vote in member ballots here - and if you hold a representative or communications role, also get access to additional reports.</p>

    @if ($ballots->count() > 0)
	<h2>Member Ballots</h2>
	<p>To cast a vote in the ballot, select your preferred option and press the "Cast Vote" button. It will not be possible to link your vote to you, and therefore you will not be able to change your vote once you have cast it.</p>

	@foreach ($ballots as $ballot)
	    <h3>{{ $ballot->title }}</h3>
	    <p><strong>Voting closes</strong>: {{ $ballot->end->format("j F Y H:i") }}</p>
	    <p>{{ $ballot->description }}</p>

	    {!! Form::open(['route' => ['ballots.vote', $ballot->id], 'method' => 'POST']) !!}
	    <fieldset><legend>Select option</legend>
	    <div>
		{!! Form::radio('option', 0, true, ['id' => 'option_0']) !!}
		{!! Form::label('option_0', '(select option)') !!}
	    </div>
	    @foreach ($ballot->options()->orderBy('order')->get() as $option)
		<div>
		    {!! Form::radio('option', $option->id, false, ['id' => 'option_'.$option->id]) !!}
		    {!! Form::label('option_'.$option->id, $option->option) !!}
		</div>
	    @endforeach
	    </fieldset><br>
	    {!! Form::submit('Cast Vote') !!}
	    {!! Form::close() !!}

	@endforeach

    @endif
    
    <h2>Campaign Actions</h2>
    <p>We ask you to record your participation in certain campaign actions, as this is essential to get an overall view of where our strength is, and ultimately to win! Effective practical solidarity requires not just participating, but participating visibly so that your colleagues can feel stronger as a result. You will also benefit by not receiving some targeted communications relating to campaign actions you've already taken.</p>

    @if ($campaigns->count() == 0)
	<p>There are no active campaign actions at the moment.</p>
    @else

	@foreach ($campaigns as $campaign)

	    <h3>{{ $campaign->name }}</h3>
	    <p>{{ $campaign->description }}</p>

	    {!! Form::open(['route' => ['participate', $campaign->id], 'method' => 'POST']) !!}

	    <div>
		{!! Form::label('participation'.$campaign->id, 'Have you participated?') !!}
		{!! Form::select('participation'.$campaign->id, $campaign->stateDescriptions("I"), $self->participation($campaign)) !!}
		{!! Form::submit("Update participation") !!}
	    </div>
	    
	    {!! Form::close() !!}

	    <p><strong>Total participation so far</strong>: {{$campaign->actions_count}} and {{ $campaign->calctarget }} required. This campaign action must be completed by {{ $campaign->end->format("j F Y") }}.</p>
	    
	@endforeach	

    @endif

    @if ($ballots->count() == 0)
	<h2>Member Ballots</h2>
	
	<p>There are no ballots currently waiting for your vote. You can view all current and previous ballots in the Ballot Archive.</p>
    @endif

    
</x-layout>
