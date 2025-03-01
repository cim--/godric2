<x-layout>
    <x-slot:title>Notices and Information</x-slot:title>

    @if ($notices->count() == 0)
	<p>No notices today.</p>
    @else
	@if (isset($notices[""]))
	<h2>General Notices</h2>
	<ul>
	    @foreach ($notices[""] as $notice)
		<li @if ($notice->highlight) class='highnotice' @endif >
		    @if ($notice->start)
			{{ $notice->start->format("j F Y") }}:
		    @endif
		    <a href="{{ route('notices.read', $notice->id) }}">{{ $notice->title }}</a>
		</li>
	    @endforeach
	</ul>
	@endif

	@foreach ($notices as $meeting => $documents)
	    @if ($meeting != "")
		<h2>{{ $meeting }}</h2>
		<ul>
		@foreach ($documents as $notice)
		    <li @if ($notice->highlight) class='highnotice' @endif >
			<a href="{{ route('notices.read', $notice->id) }}">{{ $notice->title }}</a>
		    </li>
		@endforeach
		</ul>
	    @endif
	@endforeach
    @endif
    
</x-layout>
