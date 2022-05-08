<x-layout>
    <x-slot:title>Ballot Archive</x-slot:title>

    @if ($running->count() > 0)
	<h2>Current Online Ballots</h2>
	<p>The following ballots are currently running. If eligible, you can cast your vote on the <a href="{{ route('main') }}">home page</a>. Results will be displayed here when the ballot closes.</p>

	<ul>
	    @foreach ($running as $runner)
		<li>{{ $runner->title }}: closes {{ $runner->end->format("j F Y H:i") }}</li>
	    @endforeach
	</ul>
    @endif

    @if ($ballots->count() == 0)
	<p>No online ballots have finished yet.</p>
    @else
	<p>The following online ballot results are available.</p>
    @foreach ($ballots as $ballot)
	<h2>{{ $ballot->title }}</h2>
	<p>{{ $ballot->description }}</p>
	<p>Voting between {{ $ballot->start->format("j F Y H:i") }} and {{ $ballot->end->format("j F Y H:i") }}</p>

	<ul>
	    @foreach ($ballot->options as $option)
		<li>{{ $option->option }}:
		    @if ($option->votes == 1)
			1 vote
		    @else
			{{ $option->votes }} votes
		    @endif
		</li>
	    @endforeach
	</ul>
    @endforeach
    @endif
    
</x-layout>
