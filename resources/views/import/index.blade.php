<x-layout>
    <x-slot:title>Import Membership List</x-slot:title>

    {!! Form::open(['method' => 'POST', 'route' => 'import.process', 'files' => true]) !!}

    <div>
	{!! Form::label('list', 'Upload CSV membership list') !!}
	{!! Form::file('list') !!}
    </div>

    {!! Form::submit('Process File') !!}

    <p>Warning: large files may take several minutes to import.</p>
    
    {!! Form::close() !!}

    <h2>Recent changes</h2>

    <p>Change records are kept for two weeks or since the most recent import, whichever is longer.</p>

    @if ($changelogs->count() > 0)
	<ul>
	    @foreach ($changelogs as $log)
		<li>
		    <strong>{{$log->created_at->format("Y-m-d H:i")}}</strong>
		    {{$log->message}}
		</li>
	    @endforeach
	</ul>
    @else
    <p>No recent changes recorded.</p>
    @endif
    
</x-layout>


