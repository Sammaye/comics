<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class ComicStrip extends Model
{
    //
    public function comic()
    {
        return $this->belongsTo(Comic::class);
    }
}
