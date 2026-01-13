<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Habit extends Model
{
    protected $guarded = ['id'];

    public function user(){
        $this->belongsTo(User::class);
    }
}