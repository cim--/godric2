<x-layout>
    <x-slot:title>Welcome to GODRIC</x-slot:title>

    <p>Welcome to the GODRIC campaigns management system. You can record your participation in campaigns here - and if you hold a representative or communications role, also get access to additional reports.</p>

    <p>We ask you to record your participation in certain campaign actions, as this is essential to get an overall view of where our strength is, and ultimately to win! Effective practical solidarity requires not just participating, but participating visibly so that your colleagues can feel stronger as a result. You will also benefit by not receiving some targeted communications relating to campaign actions you've already taken.</p>

    <h2>Campaign Actions</h2>
    
    @if ($campaigns->count() == 0)
	<p>There are no active campaign actions at the moment.</p>
    @else

	@foreach ($campaigns as $campaign)

	    <h3>{{ $campaign->name }}</h3>
	    <p>{{ $campaign->description }}</p>

	    {!! Form::open(['route' => ['participate', $campaign->id], 'method' => 'POST']) !!}

	    <div>
		{!! Form::label('participation'.$campaign->id, 'Have you participated?') !!}
		{!! Form::select('participation'.$campaign->id, [
		    '-' => '(select answer)',
		    'yes' => 'I have participated',
		    'wait' => 'I will participate soon',
		    'help' => 'I need assistance to participate',
		    'no' => 'I have not participated / prefer not to say, but do not need further reminders'
		    ], $self->participation($campaign)) !!}
		{!! Form::submit("Update participation") !!}
	    </div>
	    
	    {!! Form::close() !!}

	    <p><strong>Total participation so far</strong>: {{$campaign->actions_count}} and {{ $campaign->calctarget }} required. This campaign action must be completed by {{ $campaign->end->format("j F Y") }}.</p>
	    
	@endforeach	

    @endif
    
</x-layout>
