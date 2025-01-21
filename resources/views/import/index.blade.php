<x-layout>
    <x-slot:title>Import Membership List</x-slot:title>

    {!! html()->form('POST', route('import.process'))->attribute('enctype', 'multipart/form-data')->open() !!}

    <div>
	{!! html()->label('Upload CSV membership list','list') !!}
	{!! html()->file('list') !!}
    </div>

    {!! html()->submit('Process File') !!}

    <p>Warning: large files may take several minutes to import.</p>

    {!! html()->form()->close() !!}

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


