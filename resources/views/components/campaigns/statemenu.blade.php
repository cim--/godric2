@if (!$campaign->votersonly || $member->voter)

    @if ($campaign->campaigntype != App\Models\Campaign::CAMPAIGN_PETITION || $member->participation($campaign) != "yes")
	<div>
	    {!! html()->label($campaign->name,'action'.$campaign->id) !!}
	    {!! html()->select('action'.$campaign->id, $campaign->stateDescriptions("They") ,$member->participation($campaign)) !!}
	    @if ($campaign->campaigntype == App\Models\Campaign::CAMPAIGN_PETITION)
		(you cannot record signatures on their behalf)
	    @endif
	</div>
    @else
	<div>The member has signed this petition. Admins cannot edit this.</div>
    @endif
@else
    <div>
	{{$campaign->name}}: voters only
    </div>
@endif
