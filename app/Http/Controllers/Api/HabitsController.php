<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArchiveHabitRequest;
use App\Http\Requests\HabitCreateRequest;
use App\Http\Resources\HabitResource;
use App\Models\CompletedHabit;
use App\Models\Habit;
use App\Resources\App\Http\Resources\HabitsResource;
use Google\Service\ApigeeRegistry\ApiSpec;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;

class HabitsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{
            $per_page = $request->input('per_page', 10);

            $habits = Habit::where('user_id', auth()->id())
                ->with(['completed' => function ($q) {
                    $q->whereDate('created_at', today())
                    ->where('user_id', auth()->id());
                }])->paginate($per_page);

            if ($habits->isEmpty()) {
                return apiError('No habits');
            }

            return HabitResource::collection($habits)->additional([
                'status'  => 'success',
                'message' => 'Habits loaded',
            ]);
        } catch(\Exception $e) {
            return apiError($e->getMessage());
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HabitCreateRequest $request)
    {
        try{

            $habits = [
            'name' => $request->name,
            'user_id' => Auth()->user()->id,
            ];

            Habit::create($habits);

            return apiSuccess($habits['name'] . " Created.");

        } catch (\Throwable $e) {
            return apiError($e->getMessage());
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return apiSuccess('show');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(string $id)
    {
        try{
            $habit = Habit::findOrFail($id);

            if($habit->user_id === Auth()->user()->id){
                if($habit->status === 'archived') {
                    return apiError('The habit is already archived');
                }
                $habit->update([
                    'status' => 'archived'
                ]);
                
            return apiSuccess('Habit Updated',new HabitResource($habit));
            } else {
                return apiError("No permission to archive another ones habit");
            }
        } catch(\Throwable $e) {
            apiError($e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $habit = Habit::find($id);
            if(!$habit){
                return apiError('The habit not found');
            }

            if($habit->user_id === Auth()->user()->id){
                $habit->delete();
            return apiSuccess('Habit Deleted',new HabitResource($habit));
            } else {
                return apiError("No permission to archive another ones habit");
            }
        } catch(\Throwable $e) {
            apiError($e->getMessage());
        }
    }

    /**
     * Custom method complete the habit
     */
    public function complete($habit_id) {
        try{
            $habit = Habit::find($habit_id);

            $already_completed = CompletedHabit::where('user_id',auth()->id())
                                                ->where('habit_id',$habit_id)
                                                ->whereDate('created_at',now())
                                                ->exists();
            if($already_completed) {
                return apiError('The habit already completed');
            }
            
            if($habit){
                $completed_habit = [
                    'user_id' => Auth()->user()->id,
                    'habit_id' => $habit_id
                ];
                CompletedHabit::create($completed_habit);
                return apiSuccess('Habit completed for today');
            } else {
                return apiError('Habit not found');
            }
        } catch(\Throwable $e) {
            return apiError($e->getMessage());
        }

    }
}
