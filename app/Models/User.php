<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use App\Notifications\SendActivatedEmail;
use Identicon\Identicon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens,Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'signature',
        'avatar'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }


    public function sendVerifyEmail()
    {
        $this->notify(new SendActivatedEmail($this));
    }

    function  sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }


    public function getAvatarAttribute($value)
    {
        if (!$value)
        {
            $idention = new Identicon();
            $imageDataUrl = $idention->getImageDataUri( md5($this->email),400);
            return $imageDataUrl;
        }
        return $value;
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($user){
            $user->activation_token = str_random(30);
        });


        static::deleting(function ($user){
            $user->articles->each->forceDelete();
        });

    }
}
