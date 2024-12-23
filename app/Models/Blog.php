<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    function rel_to_user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    function rel_to_like_count() {
        return $this->hasMany(Like::class, 'blog_id');
    }

    function rel_to_blog_comments() {
        return $this->hasMany(Comment::class, 'blog_id');
    }

    function rel_to_blog_reply() {
        return $this->hasMany(Reply::class, 'blog_id');
    }

    function rel_to_blog_reply_20() {
        return $this->hasMany(Reply_20::class, 'blog_id');
    }

    function rel_to_blog_reply_30() {
        return $this->hasMany(Reply_30::class, 'blog_id');
    }

    public function category()
{
    return $this->belongsTo(Category::class, 'category_id');
}
}
