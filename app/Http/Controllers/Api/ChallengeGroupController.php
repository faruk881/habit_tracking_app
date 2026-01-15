<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateGroupChallengeRequest;
use App\Models\ChallengeGroup;
use App\Models\ChallengeParticipant;
use App\Models\ChallengeType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChallengeGroupController extends Controller
{

    public function viewChallengeType() {

    $challenge_type = ChallengeType::get(['id','name']);
        return response()->json([
            'status' => 'success',
            'message' => 'Challenge type loaded',
            'data' => $challenge_type
        ]);
    }

    public function createGroupChallenge(CreateGroupChallengeRequest $request){

        
        try {
            // DB::beginTransaction();
            $start = Carbon::now();
            $end   = Carbon::now()->addDays($request->duraction);
            $challenge = ChallengeGroup::create([
                'challenge_name'     => $request->challenge_name,
                'challenge_type_id'  => $request->challenge_type_id,
                'user_id'            => auth()->id(),
                'status'             => 'active',
                'challenge_start'    => $start,
                'challenge_end'      => $end,
            ]);

            $challenge->challengeHabits()->attach($request->habits_id);
            ChallengeParticipant::create([
                'challenge_group_id' => $challenge->id,
                'user_id' => auth()->id()
            ]);
            return apiSuccess('Challenge created successfully', [
                'challenge_id' => $challenge,
                'start_date'   => $start->toDateString(),
                'end_date'     => $end->toDateString(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return apiError($e->getMessage());
        }


        return response()->json([
            'status' => 'success',
            'message' => 'ss'
        ]);

    }
}
