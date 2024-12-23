<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Set_Chat_Bg extends Model
{
    function rel_to_chat_bg() {
        return $this->belongsTo(Chat_Bg::class, 'chat_bg_id');
    }
}
