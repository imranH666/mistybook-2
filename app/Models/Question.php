<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    function rel_to_answer() {
        return $this->hasMany(Answer::class, 'question_id');
    }

    function rel_to_user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
