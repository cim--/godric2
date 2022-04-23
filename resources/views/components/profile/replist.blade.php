@if (count($reps) == 0)
    <p>None - you could volunteer by contacting your <a href="#orgrep">organisational administrators</a>.</p>
@else
    <ul>
	@foreach ($reps as $rep)
	    <li>{{ $rep->firstname }} {{ $rep->lastname }}</li>
	@endforeach
    </ul>
@endif

