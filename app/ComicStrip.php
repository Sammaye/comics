<?php

namespace App;

use App\Traits\FuzzyDates;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Jenssegers\Mongodb\Eloquent\Model;

class ComicStrip extends Model
{
    use FuzzyDates;

    protected $collection = 'comic_strip';

    protected $fillable = [
        'date',
        'url',
        'image_url',
        'img',
        'image_md5',
        'skip',
        'index',
        'next',
        'previous',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function comic()
    {
        return $this->belongsTo(Comic::class);
    }

    public function getValidator($request)
    {
        foreach(['skip'] as $k => $v) {
            $request->merge([$v => (int)$request->input($v)]);
        }

        $rules = [
            'date' => [
                'nullable',
                'sometimes',
                'date_format:d/m/Y',
                'after:1600-01-01',
                'before_or_equal:' . Carbon::now()->format('Y-m-d'),
                function($attribute, $value, $fail) use($request) {
                    $request->merge([$attribute => Carbon::createFromFormat('!d/m/Y', $value)]);
                }
            ],
            'url' => 'nullable|sometimes|string|max:250',
            'image_url' => 'nullable|sometimes|string|max:250',
            'skip' => 'sometimes|integer|min:0|max:1',
            'index' => [
                'sometimes',
                'string',
                'max:250',
                Rule::unique($this->getTable())->where(function($query){
                    return $query->where('comic_id', $this->comic_id);
                })->ignore($this, '_id')

            ],
            'previous' => 'nullable|sometimes|string|max:250',
            'next' => 'nullable|sometimes|string|max:250',
        ];

        if ($this->comic->type === Comic::TYPE_DATE) {
            $rules['index'] = [
                'sometimes',
                'date_format:d/m/Y',
                Rule::unique($this->getTable())->where(function($query){
                    return $query->where('comic_id', $this->comic_id);
                })->ignore($this, '_id'),
                function($attribute, $value, $fail) use($request) {
                    $request->merge([$attribute => Carbon::createFromFormat('!d/m/Y', $value)]);
                }
            ];

            $rules['previous'] = $rules['next'] = [
                'nullable',
                'sometimes',
                'date_format:d/m/Y',
                function($attribute, $value, $fail) use($request) {
                    $request->merge([$attribute => Carbon::createFromFormat('!d/m/Y', $value)]);
                }
            ];
        }

        return Validator::make($request->all(), $rules);
    }
}
