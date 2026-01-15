<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChallengeType extends Model
{
    protected $guarded = ['id'];
    
    public function challenges(){
        return $this->hasMany(ChallengeGroup::class);
    }
}
