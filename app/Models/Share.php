<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Share extends Model
{

   function rel_to_share_user() {
    return $this->belongsTo(User::class, 'user_id');
   }
}
