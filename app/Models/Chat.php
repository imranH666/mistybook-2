<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = ['sender_id', 'recipient_id', 'sender_name', 'message', 'image', 'see'];
}
