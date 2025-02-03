<x-layout>
    <x-slot:title>Campaigns: Import Actions</x-slot:title>

    {!! html()->form('POST',route('campaigns.import.process', $campaign->id))->open() !!}

    <p>Import actions for {{ $campaign->name }}</p>

    <div>
	{!! html()->label('Import as action','action') !!}
	{!! html()->select('action', [
	    'yes' => 'Participated',
	    'wait' => 'Intends to',
	    'help' => 'Needs help',
	    'no' => 'No, end contact'
	    ] ,'yes') !!}
    </div>
    <div>
	{!! html()->label('Members','members') !!}
	{!! html()->textarea('members', '', ['rows'=>20]) !!}
	<br>
	One per line, using membership ID, email or phone
    </div>

    {!! html()->submit("Import Actions") !!}

    {!!  html()->form()->close() !!}

</x-layout>
