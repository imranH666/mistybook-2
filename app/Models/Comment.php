<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    function rel_to_user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
