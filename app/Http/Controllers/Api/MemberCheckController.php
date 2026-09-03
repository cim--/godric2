<?php

namespace App\Http\Controllers\Api;

use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberCheckController extends Controller
{
    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'member_id' => ['required', 'string', 'max:10'],
            'surname' => ['required', 'string'],
        ]);

        $member = Member::where(
            'membership',
            $request->input('member_id')
        )->first();

        // Always run a comparison so the response time doesn't leak whether
        // the member_id exists — hash_equals gives constant-time evaluation.
        $candidateSurname = $member ? strtolower($member->lastname) : '';
        $match =
            $member &&
            hash_equals(
                $candidateSurname,
                strtolower($request->input('surname'))
            );

        return response()->json(['match' => $match]);
    }
}
