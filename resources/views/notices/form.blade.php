<x-layout>
    <x-slot:title>Notices: Edit</x-slot:title>

    @if ($notice->id)
	{!! html()->form('PUT',route('notices.update', $notice->id))->open() !!}
    @else
	{!! html()-form('POST',route('notices.store', 'method'))->open() !!}
    @endif

    <div>
	{!! html()->label('meeting', 'Meeting') !!}
	{!! html()->text('meeting', $notice->meeting, ["list" => "meetings", "size" => 30]) !!} Leave blank if not associated with a meeting
	<datalist id="meetings">
	    @foreach ($meetings as $meeting)
		<option>{{ $meeting }}</option>
	    @endforeach
	</datalist>
    </div>
    <div>
	{!! html()->label('title', 'Title') !!}
	{!! html()->text('title', $notice->title) !!}
    </div>
    <div>
	{!! html()->label('content', 'Content') !!}
	{!! html()->textarea('content', $notice->content, ['class' => 'htmlbox']) !!}
	(HTML markup allowed)
    </div>
    <div>
	{!! html()->label('start', 'Start') !!}
	{!! html()->date('start', $notice->start ? $notice->start->format("Y-m-d") : "") !!}
	({!! html()->label('nostart', 'no date?') !!}
	{!! html()->checkbox('nostart', 1, $notice->start === null) !!})
	Setting a start date a few weeks before the meeting date is strongly recommended for documents associated with meetings.
    </div>
    <div>
	{!! html()->label('end', 'End') !!}
	{!! html()->date('end', $notice->end ? $notice->end->format("Y-m-d") : "") !!}
	({!! html()->label('noend', 'no date?') !!}
	{!! html()->checkbox('noend', 1, $notice->end === null) !!})
    </div>
    <div>
	{!! html()->label('highlight', 'Highlight on front page?') !!}
	{!! html()->checkbox('highlight', 1, $notice->highlight) !!}
    </div>


    {!! html()->submit("Edit Notice") !!}

    {!!  html()->form()->close() !!}


    @if ($notice->id)
	<h2>Delete notice</h2>
	{!! html()->form('DELETE', route('notices.destroy', $notice->id))->open() !!}
	<p><strong>Warning:</strong> Notice deletion cannot be undone - consider hiding it by setting the display dates if it might be needed again later.</p>

	{!! html()->submit("Delete Notice") !!}

	{!!  html()->form()->close() !!}

    @endif



</x-layout>
