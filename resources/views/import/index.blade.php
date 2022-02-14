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
    
</x-layout>


