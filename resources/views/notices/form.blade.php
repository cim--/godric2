<x-layout>
    <x-slot:title>Notices: Edit</x-slot:title>

    @if ($notice->id)
	{!! Form::open(['route' => ['notices.update', $notice->id], 'method' => 'PUT']) !!}
    @else
	{!! Form::open(['route' => 'notices.store', 'method' => 'POST']) !!}
    @endif

    <div>
	{!! Form::label('title', 'Title') !!}
	{!! Form::text('title', $notice->title) !!}
    </div>
    <div>
	{!! Form::label('content', 'Content') !!}
	{!! Form::textarea('content', $notice->content) !!}
	(HTML markup allowed)
    </div>
    <div>
	{!! Form::label('start', 'Start') !!}
	{!! Form::date('start', $notice->start ? $notice->start->format("Y-m-d") : "") !!}
	({!! Form::label('nostart', 'no date?') !!}
	{!! Form::checkbox('nostart', 1, $notice->start === null) !!})
    </div>
    <div>
	{!! Form::label('end', 'End') !!}
	{!! Form::date('end', $notice->end ? $notice->end->format("Y-m-d") : "") !!}
	({!! Form::label('noend', 'no date?') !!}
	{!! Form::checkbox('noend', 1, $notice->end === null) !!})
    </div>
    <div>
	{!! Form::label('highlight', 'Highlight on front page?') !!}
	{!! Form::checkbox('highlight', 1, $notice->highlight) !!}
    </div>

    
    {!! Form::submit("Edit Notice") !!}

    {!!  Form::close() !!}


    @if ($notice->id)
	<h2>Delete notice</h2>
	{!! Form::open(['route' => ['notices.destroy', $notice->id], 'method' => 'DELETE']) !!}
	<p><strong>Warning:</strong> Notice deletion cannot be undone - consider hiding it by setting the display dates if it might be needed again later.</p>
	
	{!! Form::submit("Delete Notice") !!}

	{!!  Form::close() !!}

    @endif


    
</x-layout>
