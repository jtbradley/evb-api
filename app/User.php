<?php

namespace App;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject

{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function ownsTopic(Topic $topic) {
        return $this->id === $topic->user->id;
    }

    public function ownsPost(Post $post) {
        return $this->id === $post->user->id;
    }

    public function hasLikedPost(Post $post) {
        return $post->likes->where('user_id', $this->id)->count() === 1;
    }

    public function getJWTIdentifier()
    {
        // return primary key of user - user id
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        // return a key value aray containing any custom claims to be added to JWT
        return [];
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
