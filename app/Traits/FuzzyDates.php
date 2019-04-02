<?php
namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use MongoDB\BSON\UTCDateTime;

trait FuzzyDates
{
    public function setAttribute($key, $value)
    {
        if ($value instanceof Carbon) {
            Arr::set($this->attributes, $key, $this->fromDateTime($value));

            return;
        }
        return parent::setAttribute($key, $value);
    }

    public function getAttributeValue($key)
    {
        $value = $this->getAttributeFromArray($key);

        if ($value instanceof UTCDateTime) {
            return $this->asDateTime($value);
        }

        return parent::getAttributeValue($key);
    }
}
