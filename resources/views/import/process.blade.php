<x-layout>
    <x-slot:title>Membership List Processed</x-slot:title>

    @if ($excelcheck)
	<div class="error"><p><strong>Warning: the phone numbers in this import file appear misformatted.</strong></p><p>This is often caused by the file being opened in Excel or other spreadsheet software before being imported. Download and save the file, then load it into Godric without importing for best results.</p></div>
    @endif
    
    <p>Imported {{$total}} members.</p>

    <h2>New Members</h2>

    @if (count($added) == 0)
	<p>No new members</p>
    @else
	<p>{{count($added)}} new members</p>
	<ul>
	@foreach($added as $add)
	    <li>{{ $add }}</li>
	@endforeach
	</ul>
    @endif

    <h2>Removed Members</h2>

    @if (count($removed) == 0)
	<p>No removed members</p>
    @else
	<p>{{count($removed)}} removed members</p>
	<ul>
	    @foreach($removed as $rem)
		<li>{{ $rem }}</li>
	    @endforeach
	</ul>
    @endif
    

    
</x-layout>


