<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Following_Follower extends Model
{
    function rel_to_user() {
        return $this->belongsTo(User::class, 'follower');
    }
}
