<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];
    protected $guard = 'user';

    function rel_to_follower() {
        return $this->hasMany(Following_Follower::class, 'follower');
    }

    function rel_to_post_count() {
        return $this->hasMany(Post::class, 'user_id');
    }

    function rel_to_blog_count() {
        return $this->hasMany(Blog::class, 'user_id');
    }

    function rel_to_question_count() {
        return $this->hasMany(Question::class, 'user_id');
    }

    function rel_to_video_count() {
        return $this->hasMany(Video::class, 'user_id');
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
