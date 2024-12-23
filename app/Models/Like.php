<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    function rel_to_like_user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
