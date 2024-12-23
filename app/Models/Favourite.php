<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    function rel_to_answer() {
        return $this->hasMany(Answer::class, 'question_id');
    }

    function rel_to_user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    function rel_to_question() {
        return $this->belongsTo(Question::class, 'question_id');
    }

    function rel_to_post() {
        return $this->belongsTo(Post::class, 'post_id');
    }

    function rel_to_blog() {
        return $this->belongsTo(Blog::class, 'blog_id');
    }

    function rel_to_video() {
        return $this->belongsTo(Video::class, 'video_id');
    }

}
