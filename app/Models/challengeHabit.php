<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class challengeHabit extends Model
{
    protected $guarded = ['id'];

    public function ChallengeGroup(){
        return $this->belongsToMany(
        Habit::class,
        'challenge_habits',        // pivot table name
        'challenge_group_id',     // FK of THIS model
        'habit_id'                // FK of OTHER model
    );
    }
}
