<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompletedHabit extends Model
{
    protected $guarded = ['id'];
    
    public function habit(){
        return $this->belongsTo(Habit::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
}
