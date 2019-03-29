<?php

namespace App;

use Jenssegers\Mongodb\Auth\User as Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use sammaye\Permission\Traits\HasPermissions;

class User extends Authenticatable{

    use MustVerifyEmail, Notifiable, HasPermissions;

    protected $attributes = [
        'email_frequency' => 'daily',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'email_frequency',
        'google_id',
        'facebook_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'blocked_at' => 'datetime',
    ];

    public function getEmailFrequencies()
    {
        return [
            'daily' => __('Daily'),
            'weekly' => __('Weekly'),
            'monthly' => __('Monthly'),
            'paused' => __('Paused'),
        ];
    }

    public function isBlocked()
    {
        return !is_null($this->blocked_at);
    }
}
