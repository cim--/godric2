<x-layout>
    <x-slot:title>Welcome to GODRIC</x-slot:title>

    @if ($notices->count() > 0)
	<ul>
	    @foreach ($notices as $notice)
		<li class="highnotice">
		    <a href="{{ route('notices.read', $notice->id) }}">{{ $notice->meeting }} {{ $notice->title }}</a>
		</li>
	    @endforeach
	</ul>
    @endif

    <p>Welcome to the GODRIC system. You can record your participation in campaigns and vote online here - and if you hold a representative or communications role, also get access to additional reports.</p>

    @if ($ballots->count() > 0)
	<h2>Member Online Votes</h2>
	<p>To cast a vote online, select your preferred option and press the "Cast Vote" button. <strong>It will not be possible to link your vote to you, and therefore you will not be able to change your vote once you have cast it.</strong></p>

	@foreach ($ballots as $ballot)
	    <h3>{{ $ballot->title }}</h3>
	    <p><strong>Voting closes</strong>: {{ $ballot->end->format("j F Y H:i") }}</p>
	    <p>{!! $ballot->description !!}</p>

	    {!! html()->form('POST', route('ballots.vote', $ballot->id))->open() !!}
	    <fieldset><legend>Select option</legend>
	    <div>
		{!! html()->radio('option', true, 0)->attributes(['id' => 'option_0']) !!}
		{!! html()->label('(select option)','option_0') !!}
	    </div>
	    @foreach ($ballot->options()->orderBy('order')->get() as $option)
		<div>
		    {!! html()->radio('option', false, $option->id)->attributes(['id' => 'option_'.$option->id]) !!}
		    {!! html()->label($option->option,'option_'.$option->id) !!}
		</div>
	    @endforeach
	    </fieldset><br>
	    {!! html()->submit('Cast Vote') !!}
	    {!! html()->form()->close() !!}

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

	    {!! html()->form('POST',route('participate', $campaign->id))->open() !!}

	    <div>
		{!! html()->label('Have you participated?','participation'.$campaign->id) !!}
		{!! html()->select('participation'.$campaign->id, $campaign->stateDescriptions("I"), $self->participation($campaign)) !!}
		{!! html()->submit("Update participation") !!}
	    </div>

	    {!! html()->form()->close() !!}

	    <p><strong>Total participation so far</strong>: {{$campaign->actions_count}} and {{ $campaign->calctarget }} required. This campaign action must be completed by {{ $campaign->end->format("j F Y") }}.</p>

	@endforeach

    @endif

    @if ($ballots->count() == 0)
	<h2>Member Online Votes</h2>

	<p>There are no polls currently waiting for your vote. You can view all current and previous online votes in the <a href="{{ route('ballots.archive') }}">Archive</a>.</p>
    @endif


</x-layout>
