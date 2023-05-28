<nav>
    <ul>
	<li><a href="{{route('main')}}">Home</a></li>
	<li><a href="{{route('notices.public')}}">Notices and Information</a></li>
	<li><a href="{{route('profile')}}">My Profile</a></li>
	<li><a href="{{route('ballots.archive')}}">Online Vote Archive</a></li>

	<li><a href="{{route('auth.password')}}">Change Password</a></li>
	<li><a href="{{route('auth.logout')}}">Log out {{ Auth::user()->username ?? "" }}</a></li>
    </ul>

    @can('seeReports', App\Models\Member::class)
    <ul>
        @can('seeLists', App\Models\Member::class)
	<li><a href="{{route('members.list')}}">Member Lists</a></li>
	@endcan
	
	<li><a href="{{route('campaign.report')}}">Campaign Reports</a></li>

	@can('seePhonebank', App\Models\Member::class)
	<li><a href="{{route('phonebank')}}">Campaign Participation</a></li>
	@endcan
    </ul>
    @endcan
    
    @can('manage', App\Models\Member::class)
    <ul>
	<li><a href="{{route('import')}}">Import Members</a></li>
	<li><a href="{{route('roles.index')}}">Set up Roles</a></li>
	<li><a href="{{route('campaigns.index')}}">Set up Campaigns</a></li>
	<li><a href="{{route('ballots.index')}}">Set up Online Votes</a></li>
	<li><a href="{{route('notices.index')}}">Set up Notices</a></li>
	<li><a href="{{route('workplaces.index')}}">Set up Workplaces</a></li>
    </ul>
    @endcan
</nav>

