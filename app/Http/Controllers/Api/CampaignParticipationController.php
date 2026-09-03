<?php

namespace App\Http\Controllers\Api;

use App\Models\Action;
use App\Models\Campaign;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CampaignParticipationController extends Controller
{
    // Currently-active campaigns, matching the same scopeStarted() window
    // used throughout the rest of the app.
    public function index(): JsonResponse
    {
        $campaigns = Campaign::started()
            ->orderBy('end')
            ->get(['id', 'name']);

        return response()->json($campaigns);
    }

    public function update(Request $request, Campaign $campaign): JsonResponse
    {
        $validStatuses = array_diff(
            array_keys($campaign->stateDescriptions('I')),
            ['-']
        );

        $request->validate([
            'member_id' => ['required', 'string', 'max:10'],
            'status' => [
                'required',
                'string',
                'in:' . implode(',', $validStatuses),
            ],
        ]);

        if (
            $campaign->start->isFuture() ||
            $campaign->end->copy()->addDay()->isPast()
        ) {
            throw ValidationException::withMessages([
                'campaign' => 'This campaign is not currently active',
            ]);
        }

        $member = Member::where(
            'membership',
            trim($request->input('member_id'))
        )->first();

        // Mirrors MemberCheckController: the response shape and status code
        // are identical whether the member doesn't exist or the votersonly
        // gate fails, so a caller can't use this endpoint to probe which
        // membership numbers are real.
        if (!$member || ($campaign->votersonly && !$member->voter)) {
            return response()->json(['updated' => false]);
        }

        $action = Action::firstOrNew([
            'campaign_id' => $campaign->id,
            'member_id' => $member->id,
        ]);
        $action->action = $request->input('status');
        $action->save();

        return response()->json(['updated' => true]);
    }
}
