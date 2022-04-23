<x-layout>
    <x-slot:title>Your profile</x-slot:title>

    <p>Your profile page allows you to view the data held in Godric for you.</p>

    <div class="profilebox">
	<h2>Contact information</h2>

	<p>This information comes from national membership records. If it is incorrect, please adjust it in the <a href="https://my.ucu.org.uk">national records</a> and it should then be corrected here in a few working days. If you have difficulty getting it corrected, please contact your <a href="#orgrep">organisational administrators</a>.</p>

	<table>
	    <tbody>
		<tr>
		    <th scope="row">Membership ID</th>
		    <td>{{ $self->membership }}</td>
		    <td></td>
		</tr>
		<tr>
		    <th scope="row">First Name</th>
		    <td>{{ $self->firstname }}</td>
		    <td></td>
		</tr>
		<tr>
		    <th scope="row">Last Name</th>
		    <td>{{ $self->lastname }}</td>
		    <td></td>
		</tr>
		<tr>
		    <th scope="row">Email</th>
		    <td>{{ $self->email }}</td>
		    <td>You can enter multiple emails nationally. The one you have chosen as your preferred email will be used here.</td>
		</tr>
		<tr>
		    <th scope="row">Phone</th>
		    <td>{{ $self->mobile }}</td>
		    <td>You can enter multiple phone numbers nationally. Your 'mobile' number will be used if available, your 'home' number otherwise.</td>
		</tr>
		<tr>
		    <th scope="row">Department</th>
		    <td>{{ $self->department }}</td>
		    <td></td>
		</tr>
		<tr>
		    <th scope="row">Job Type</th>
		    <td>{{ $self->jobtype }}</td>
		    <td></td>
		</tr>
		<tr>
		    <th scope="row">Member Type</th>
		    <td>{{ $self->membertype }}</td>
		    <td>This can affect whether you receive a vote.</td>
		</tr>
		<tr>
		    <th scope="row">Voter</th>
		    <td>{{ $self->voter ? "Yes" : "No" }}</td>
		    <td></td>
		</tr>
	    </tbody>
	</table>
    </div>
    <div class="profilebox">

	<h2>Workplaces</h2>

	<p>Workplaces are groups of members - they may be part of a department or independent of the departmental structure.</p>

	@if ($self->workplaces->count() == 0)
	    <p>You are not in any workplaces yet. If you are able to help map the workplaces, please volunteer by contacting your <a href="#localrep">local representatives</a>.</p>
	@else
	    <p>You are in the following workplace(s):</p>
	    <ul>
		@foreach ($self->workplaces as $workplace)
		    <li>{{ $workplace->name }}</li>
		@endforeach
	    </ul>
	    
	    <p>If this is incorrect, please contact your <a href="#localrep">local representatives</a>. You can also contact your <a href="#orgrep">organisation administrators</a> to set up new workplaces if the list above is incomplete.</p>
	@endif
    </div>
    <div class="profilebox">

	<h2>Campaign Actions</h2>

	@if ($self->actions->count() == 0)
	    <p>You have not recorded participation in any campaign actions yet.</p>
	@else

	    <p>You have recorded participation in the following campaign actions:</p>
	    <ul>
		@foreach ($self->actions as $action)
		    <li>{{ $action->campaign->name }}:
			@if ($action->action == "yes")
			    participated
			@elseif ($action->action == "wait")
			    intended to participate
			@elseif ($action->action == "help")
			    asked for help to participate
			@elseif ($action->action == "no")
			    did not participate
			@endif
		    </li>
		@endforeach
	    </ul>

	@endif
	<p>If this is incorrect or could be updated, please contact your <a href="#orgrep">organisation administrators</a>. You can <a href="{{ route('main') }}">update participation in ongoing campaigns</a> on the front page.</p>
    </div>
    <div class="profilebox">

	<h2>Roles</h2>

	@if ($self->roles->count() == 0)
	    <p>You do not have any data management roles in Godric.</p>
	@else
	    <p>You have the following data management role(s):</p>
	    <ul>
		@foreach ($self->roles as $role)
		    <li>
			{{ ucwords($role->role) }} -
			@if ($role->restrictfield == "")
			    all members
			@else
			    {{ App\Models\Role::roleFields()[$role->restrictfield] }} = {{ $role->restrictvalue }}
			@endif
		    </li>
		@endforeach
	    </ul>
	@endif

	<p>If you would like to volunteer to take on additional roles to help with campaigns or membership communication and democracy, please contact your <a href="#orgrep">organisation administrators</a>.</p>
    </div>
    <div class="profilebox">

	
	<h2>Notes</h2>
	<p>Your local representatives may make notes on your record to help administer campaigns and communications. You can see those notes here for transparency.</p>
	
	@if ($self->notes == "")
	    <p>No notes have been recorded</p>
	@else
	    <p>{{ $self->notes }}</p>
	@endif
    </div>
    <div class="profilebox">

	<h2 id="localrep">Your Local Representatives</h2>

	<p>The following members are your local representatives. Talk to them if you can volunteer to help with campaigns or communications.</p>

	<h3>For your department: {{ $self->department }}</h3>
	<x-profile.replist :reps="$reps['department']" />
	
	<h3>For your jobtype: {{ $self->jobtype }}</h3>
	<x-profile.replist :reps="$reps['jobtype']" />

	<h3>For your membership type: {{ $self->membertype }}</h3>
	<x-profile.replist :reps="$reps['membertype']" />
	@if ($self->workplaces->count() > 0)
	    <h3>For your workplaces:</h3>
	    <x-profile.replist :reps="$reps['workplace']" />
	@endif

    </div>
    <div class="profilebox">
	<h2 id="orgrep">Your Organisational Administrators</h2>

	<p>Your organisational administrators are:</p>

	<x-profile.replist :reps="$reps['organisation']" />
	
    </div>
</x-layout>
