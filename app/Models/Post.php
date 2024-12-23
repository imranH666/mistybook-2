<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = ['id'];

    function rel_to_user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    function rel_to_post_images() {
        return $this->hasMany(Post_image::class, 'post_id');
    }

    function rel_to_post_comments() {
        return $this->hasMany(Comment::class, 'post_id');
    }

    function rel_to_post_reply() {
        return $this->hasMany(Reply::class, 'post_id');
    }

    function rel_to_post_reply_20() {
        return $this->hasMany(Reply_20::class, 'post_id');
    }

    function rel_to_post_reply_30() {
        return $this->hasMany(Reply_30::class, 'post_id');
    }

    function rel_to_like() {
        return $this->belongsTo(Like::class, 'user_id');
    }

    function rel_to_like_count() {
        return $this->hasMany(Like::class, 'post_id');
    }

    function rel_to_share() {
        return $this->belongsTo(User::class, 'share_id');
    }

    function rel_to_share_count() {
        return $this->hasMany(Share::class, 'post_id');
    }
    function rel_to_testing() {
        return $this->hasMany(Share::class, 'post_id');
    }
}
