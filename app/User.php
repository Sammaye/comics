<?php

namespace App;

use App\Traits\HasObjectId;
use Carbon\Carbon;
use Jenssegers\Mongodb\Auth\User as Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use sammaye\Permission\Traits\HasPermissions;

class User extends Authenticatable{

    use HasObjectId, MustVerifyEmail, Notifiable, HasPermissions;

    protected $collection = 'user';

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
        'last_feed_sent',
        'comics',
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
        'last_feed_sent' => 'datetime',
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

    public function setComicsAttribute($value)
    {
        $value = is_array($value) ? $value : [];
        foreach ($value as $k => $comic) {
            if ($comic['date'] instanceof Carbon) {
                $value[$k]['date'] =
                    new UTCDateTime($comic['date']->getTimestamp() * 1000);
            }
        }
        $this->attributes['comics'] = $value;
    }

    public function getComicsAttribute($value)
    {
        $value = is_array($value) ? $value : [];
        foreach ($value as $k => $comic) {
            if ($comic['date'] instanceof UTCDateTime) {
                $value[$k]['date'] = new Carbon($comic['date']->toDateTime()
                    ->getTimestamp());
            }
        }
        return $value;
    }

    public function addComic($id)
    {
        $comics = is_array($this->comics) ? $this->comics : [];
        foreach ($comics as $comic) {
            if ((String)$comic['comic_id'] === (String)$id) {
                return true;
            }
        }

        $comics[] = [
            'date' => new Carbon('now'),
            'comic_id' => $id instanceof ObjectID ? $id : new ObjectID($id)
        ];
        $this->comics = $comics;
        $this->save();

        return true;
    }

    public function removeComic($id)
    {
        $comics = is_array($this->comics) ? $this->comics : [];
        foreach ($comics as $k => $comic) {
            if ((String)$comic['comic_id'] === (String)$id) {
                unset($comics[$k]);
            }
        }
        $this->comics = $comics;
        $this->save();

        return true;
    }

    public function modifyComics($subs)
    {
        $currentSubs = is_array($this->comics) ? $this->comics : [];
        if (count($currentSubs) <= 0) {
            // Cannot resolve nuttin
            return $this;
        }
        $newSubs = [];

        if (is_array($subs) && count($subs) > 0) {
            foreach ($currentSubs as $k => $sub) {
                foreach ($subs as $sk => $subKey) {
                    if ($subKey === (String)$sub['comic_id']) {
                        $newSubs[$sk] = $sub;
                    }
                }
            }
        }
        ksort($newSubs);
        $this->comics = $newSubs;
        return $this;
    }

    public function hasComic($id)
    {
        if ($id instanceof ObjectID) {
            $id = (String)$id;
        }

        if (!is_array($this->comics)) {
            return false;
        }

        foreach ($this->comics as $comic) {
            if ((String)$comic['comic_id'] === $id) {
                return true;
            }
        }
        return false;
    }

    public function currentComics() {
        $subs = [];

        foreach ($this->comics as $k => $comic) {
            if ($comic = Comic::find($comic['comic_id'])) {
                $subs[] = [
                    'id' => $comic->id->__toString(),
                    'title' => $comic->title
                ];
            }
        }

        return $subs;
    }
}
