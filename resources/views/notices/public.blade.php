<x-layout>
    <x-slot:title>Notices and Information</x-slot:title>

    @if ($notices->count() == 0)
	<p>No notices today.</p>
    @else
    <ul>
	@foreach ($notices as $notice)
	    <li @if ($notice->highlight) class='highnotice' @endif >
		@if ($notice->start)
		    {{ $notice->start->format("j F Y") }}:
		@endif
		<a href="{{ route('notices.read', $notice->id) }}">{{ $notice->title }}</a>
	    </li>
	@endforeach
    </ul>
    @endif
    
</x-layout>
