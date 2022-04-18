<nav>
    <ul>
	<li><a href="{{route('main')}}">Home</a></li>
	<li><a href="{{route('notices.public')}}">Notices and Information</a></li>

        @can('seeLists', App\Models\Member::class)
	<li><a href="{{route('members.list')}}">Member Lists</a></li>
	@endcan
	
	@can('seeReports', App\Models\Member::class)
	<li><a href="{{route('campaign.report')}}">Campaign Reports</a></li>
	@endcan

	@can('seePhonebank', App\Models\Member::class)
	<li><a href="{{route('phonebank')}}">Campaign Participation</a></li>
	@endcan

	<li><a href="{{route('auth.password')}}">Change Password</a></li>
	<li><a href="{{route('auth.logout')}}">Log out</a></li>
    </ul>

</nav>
@can('manage', App\Models\Member::class)
<nav>
    <ul>
	<li><a href="{{route('import')}}">Import Members</a></li>
	<li><a href="{{route('roles.index')}}">Set up Roles</a></li>
	<li><a href="{{route('campaigns.index')}}">Set up Campaigns</a></li>
	<li><a href="{{route('notices.index')}}">Set up Notices</a></li>
	<li><a href="{{route('workplaces.index')}}">Set up Workplaces</a></li>
    </ul>
</nav>
@endcan
