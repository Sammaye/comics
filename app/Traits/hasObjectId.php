<?php
namespace App\Traits;

use MongoDB\BSON\ObjectId;

trait HasObjectId
{
    public function getIdAttribute($value = null)
    {
        return new ObjectId(parent::getIdAttribute($value));
    }
}
