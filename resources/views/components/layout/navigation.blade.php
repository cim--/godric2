<nav>
    <ul>
	<li><a href="{{route('main')}}">Index</a></li>


	
	@can('manage', App\Models\Member::class)
	<li><a href="{{route('import')}}">Import Members</a></li>
	@endcan
	<li><a href="{{route('auth.password')}}">Change Password</a></li>
	<li><a href="{{route('auth.logout')}}">Log out</a></li>
    </ul>
</nav>
