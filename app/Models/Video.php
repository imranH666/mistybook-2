<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    function rel_to_user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
