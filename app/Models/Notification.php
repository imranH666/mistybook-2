<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $guarded = ['id'];

    function rel_to_user() {
        return $this->belongsTo(User::class, 'from_notification');
    }

    function rel_to_follower() {
        return $this->hasMany(Following_Follower::class, 'follower');
    }
}
