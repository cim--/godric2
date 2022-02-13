<!DOCTYPE html>
<html>
    <head>
	<title>{{ $title }}</title>
    </head>
    <body>

	<h1>{{ $title }}</h1>

	@if (Auth::user())
	<header>
	    <nav>
		<ul>
		    <li><a href="{{route('main')}}">Index</a></li>

		    

		    <li><a href="{{route('auth.password')}}">Change Password</a></li>
		    <li><a href="{{route('auth.logout')}}">Log out</a></li>
		</ul>
	    </nav>
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
