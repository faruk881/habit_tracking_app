<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChallengeGroup extends Model
{
    protected $guarded = ['id'];

    public function challengeType() {
        return $this->hasOne(ChallengeType::class);
    }

    public function challengeParticipants() {
        return $this->hasMany(ChallengeParticipant::class);
    }

    public function challengeHabits() {
        return $this->belongsToMany(Habit::class,'challenge_habits','challenge_group_id','habit_id');
    }
}
