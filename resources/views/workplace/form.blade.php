<x-layout>
    <x-slot:title>Workplaces: Edit</x-slot:title>

    @if ($workplace->id)
	{!! html()->form('PUT',route('workplaces.update', $workplace->id))->open() !!}
    @else
	{!! html()->form('POST',route('workplaces.store'))->open() !!}
    @endif

    <div>
	{!! html()->label( 'Name','name') !!}
	{!! html()->text('name', $workplace->name) !!} (must be unique)
    </div>
    <div>
	{!! html()->label('Import Members','newmembers') !!}
	{!! html()->textarea('newmembers', '') !!} (member id, email or phone; one per line)
    </div>

    @if ($workplace->id)
	<fieldset><legend>Current Members</legend>
	    @foreach ($workplace->members()->orderBy('lastname')->get() as $member)
		<div>
		    {!! html()->checkbox('detach[]', $member->id, false, ['id' => 'detach_'.$member->id]) !!}
		    {!! html()->label($member->membership.": ".$member->firstname." ".$member->lastname." (".$member->department.")",'detach_'.$member->id) !!}
		</div>
	    @endforeach
	</fieldset>
    @endif

    {!! html()->submit("Edit Workplace") !!}

    {!!  html()->form()->close() !!}


    @if ($workplace->id)
	{!! html()->form('DELETE',route('workplaces.destroy', $workplace->id))->open() !!}
	<p><strong>Warning:</strong> Workplace deletion cannot be undone.</p>
	<div>
	    {!! html()->label('Confirm by typing workplace name','confirm') !!}
	    {!! html()->text('confirm') !!}
	</div>

	{!! html()->submit("Delete Workplace") !!}

	{!!  html()->form()->close() !!}
    @endif

</x-layout>
