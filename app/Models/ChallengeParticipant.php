<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChallengeParticipant extends Model
{
    protected $guarded = ['id'];

    public function challengeGroup(){
        return $this->belongsTo(ChallengeGroup::class);
    }
}
