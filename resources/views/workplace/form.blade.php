<x-layout>
    <x-slot:title>Workplaces: Edit</x-slot:title>

    @if ($workplace->id)
	{!! Form::open(['route' => ['workplaces.update', $workplace->id], 'method' => 'PUT']) !!}
    @else
	{!! Form::open(['route' => 'workplaces.store', 'method' => 'POST']) !!}
    @endif

    <div>
	{!! Form::label('name', 'Name') !!}
	{!! Form::text('name', $workplace->name) !!} (must be unique)
    </div>
    <div>
	{!! Form::label('newmembers', 'Import Members') !!}
	{!! Form::textarea('newmembers', '') !!} (member id, email or phone; one per line)
    </div>

    @if ($workplace->id)
	<fieldset><legend>Current Members</legend>
	    @foreach ($workplace->members()->orderBy('lastname')->get() as $member)
		<div>
		    {!! Form::checkbox('detach[]', $member->id, false, ['id' => 'detach_'.$member->id]) !!}
		    {!! Form::label('detach_'.$member->id, $member->membership.": ".$member->firstname." ".$member->lastname." (".$member->department.")") !!}
		</div>
	    @endforeach
	</fieldset>
    @endif
    
    {!! Form::submit("Edit Workplace") !!}

    {!!  Form::close() !!}

    
    @if ($workplace->id)
	{!! Form::open(['route' => ['workplaces.destroy', $workplace->id], 'method' => 'DELETE']) !!}
	<p><strong>Warning:</strong> Workplace deletion cannot be undone.</p>
	<div>
	    {!! Form::label('confirm', 'Confirm by typing workplace name') !!}
	    {!! Form::text('confirm') !!}
	</div>
	
	{!! Form::submit("Delete Workplace") !!}

	{!!  Form::close() !!}
    @endif
    
</x-layout>
