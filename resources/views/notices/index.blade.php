<x-layout>
    <x-slot:title>Notice Editing</x-slot:title>

    <p>Notices are shown in the "Notices and Information" link, and may have highlighted links on the front page.</p>

    <p><a href="{{ route('notices.create') }}">New notice</a></p>
    
    <table class="datatable">
	<thead>
	    <tr>
		<th>Title</th>
		<th>Start</th>
		<th>End</th>
		<th>Highlighted?</th>
	    </tr>
	</thead>
	<tbody>
	    @foreach ($notices as $notice)
		<tr>
		    <td>
			<a href="{{ route('notices.edit', $notice->id) }}">{{ $notice->title }}</a>
		    </td>
		    <td data-sort="{{ $notice->start ? $notice->start->format("Y-m-d") : 0 }}">
			{{ $notice->start ? $notice->start->format("j F Y") : "" }}
		    </td>
		    <td data-sort="{{ $notice->end ? $notice->end->format("Y-m-d") : 0 }}">
			{{ $notice->end ? $notice->end->format("j F Y") : "" }}
		    </td>
		    <td>{{ $notice->highlight ? "Y" : "N" }}</td>
		</tr>
	    @endforeach
	</tbody>
    </table>
    
</x-layout>
