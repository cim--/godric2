<nav>
    <ul>
	<li><a href="{{route('main')}}">Home</a></li>

        @can('seeReports', App\Models\Member::class)
	<li><a href="{{route('members.list')}}">Member Lists</a></li>
	<li><a href="{{route('campaign.report')}}">Campaign Reports</a></li>
	@endcan
	
	@can('manage', App\Models\Member::class)
	<li><a href="{{route('import')}}">Import Members</a></li>
	<li><a href="{{route('roles.index')}}">Set up Roles</a></li>
	<li><a href="{{route('campaigns.index')}}">Set up Campaigns</a></li>
	@endcan
	<li><a href="{{route('auth.password')}}">Change Password</a></li>
	<li><a href="{{route('auth.logout')}}">Log out</a></li>
    </ul>
</nav>
