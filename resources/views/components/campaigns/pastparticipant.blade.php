@if ($part == "yes" || $part == "wait" || $part == "help")
    <span title="{{ $title }} = {{ $part }}">
	@if ($part == "yes")
	    &#x2714;&#xFE0E;
	@else
	    &#x2753;&#xFE0E;
	@endif
    </span>
@endif
