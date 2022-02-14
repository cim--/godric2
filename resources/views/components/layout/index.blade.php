<!DOCTYPE html>
<html>
    <head>
	<title>{{ $title }}</title>
    </head>
    <body>

	<h1>{{ $title }}</h1>

	@if (Auth::user())
	<header>
	    <x-layout.navigation/>
	</header>
	@endif

	@if (session()->has('message'))
	    <div id="message">
		{{session('message')}}
	    </div>
	@endif

	<div id="main">

	    {{ $slot }}

	</div>

	<footer>
	    GODRIC: the General Organising Database for Reporting Including Charting.
	</footer>
    </body>
</html>
