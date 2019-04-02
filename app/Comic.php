<?php

namespace App;

use App\Scrapers\BaseScraper;
use App\Traits\FuzzyDates;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\TransferStats;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use InvalidArgumentException;
use Jenssegers\Mongodb\Eloquent\Model;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class Comic extends Model
{
    use FuzzyDates;

    const TYPE_DATE = 0;
    const TYPE_ID = 2;

    protected $collection = 'comic';

    protected $userAgents = [
        'Google Bot' => 'Googlebot/2.1 (http://www.googlebot.com/bot.html)',
        'Chrome User' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36',
    ];

    protected $fillable = [
        'title',
        'slug',
        'description',
        'abstract',
        'scrape_url',
        'homepage',
        'author',
        'author_homepage',
        'type',

        'scraper',
        'base_url',
        'image_dom_path',
        'nav_dom_path',
        'nav_url_regex',
        'nav_next_dom_path',
        'nav_previous_dom_path',
        'nav_page_number_dom_path',
        'scraper_user_agent',
        'index_format',
        'current_index',
        'last_index',
        'first_index',
        'index_step',

        'active',
        'live',
        'classic_edition',
        'last_checked',
    ];

    protected $casts = [
        'last_checked' => 'datetime',
    ];

    private $installedScrapers;
    private $scraperObject;
    private $scrapeErrors = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct([
            'active' => 1,
            'live' => 1,
            'type' => self::TYPE_ID,
            'scraper' => 'BaseScraper',
            'scraper_user_agent' => $this->userAgents['Google Bot'],
        ] + $attributes);
    }

    protected static function boot()
    {
        parent::boot();

        self::creating(function($model){
            $model->slug = Str::slug($model->title);
            if ($model->description) {
                $model->abstract = Str::limit($model->description, 150);
            }
        });

        self::updating(function($model){
            $model->slug = Str::slug($model->title);
            if ($model->isDirty('description')) {
                $model->abstract = Str::limit($model->description, 150);
            } else {
                $model->abstract = null;
            }
        });
    }

    public function strips()
    {
        return $this->hasMany(ComicStrip::class);
    }

    public function getValidator($request)
    {
        foreach(['type', 'classic_edition', 'active', 'live'] as $k => $v) {
            $request->merge([$v => (int)$request->input($v)]);
        }

        $rules = [
            'title' => 'required|string|max:250',
            'slug' => 'sometimes|nullable|string|max:250',
            'description' => 'sometimes|nullable|string|max:1500',
            'abstract'  => 'sometimes|nullable|string|max:250',
            'scrape_url' => 'required|string|max:250',
            'base_url' => 'sometimes|nullable|string|max:250',
            'homepage' => 'sometimes|nullable|url',
            'author' => 'sometimes|nullable|string|max:400',
            'author_homepage' => 'sometimes|nullable|url',
            'type' => [
                'required',
                'integer',
                Rule::in(array_keys($this->getTypes()))
            ],
            'scraper' => [
                'required',
                'string',
                Rule::in(array_keys($this->getScrapers())),
            ],
            'image_dom_path' => 'required|string|max:400',
            'nav_url_regex' => 'sometimes|nullable|string|max:400',
            'nav_next_dom_path' => 'sometimes|nullable|string|max:400',
            'nav_previous_dom_path' => 'sometimes|nullable|string|max:400',
            'nav_page_number_dom_path' => 'sometimes|nullable|string|max:400',
            'scraper_user_agent' => 'sometimes|nullable|string|max:1500',
            'classic_edition' => 'sometimes|nullable|integer|min:0|max:1',
            'active' => 'sometimes|nullable|integer|min:0|max:1',
            'live' => 'sometimes|nullable|integer|min:0|max:1',
            'index_step' => [
                'sometimes',
                'nullable',
                'string',
                'max:250',
                function($attribute, $value, $fail) use($request) {
                    if ($request->input('type') === self::TYPE_DATE) {
                        if (preg_match('#^\d+$#', $value)) {
                            // If it is an int then let's add the default "day" step
                            $value = $value . ' day';
                        }
                        if (!preg_match('#^([0-9]+)\s+(year|month|week|day)#', $value)) {
                            $fail(__('The index step is not a valid syntax'));
                        }
                    } elseif ($request->input('type') === self::TYPE_ID) {
                        if (!$this->isIndexInt($request->input('current_index'))) {
                            $fail(__('Cannot use index step on string IDs'));
                        }

                        if (!preg_match('#^\d+$#', $value)) {
                            $fail(__('The index step for ID should be an int'));
                        }
                        $value = (int)$value;
                        if ($value <= 0) {
                            $fail(__('The index step must be greater than 0'));
                        }

                    }
                }
            ],
            'index_format' => [
                'sometimes',
                'nullable',
                'string',
                'max:250',
                function($attribute, $value, $fail) use($request) {
                    if (
                        $request->input('type') === self::TYPE_DATE &&
                        (
                            !preg_match('/[d]/i', $value) ||
                            !preg_match('/[m]/i', $value) ||
                            !preg_match('/[y]/i', $value)
                        )
                    ) {
                        $fail(__('The index format must be valid date syntax'));
                    }
                }
            ],
            'current_index' => [
                'required',
                'string',
                'max:250',
                $request->input('type') === self::TYPE_ID &&
                $this->isIndexInt($request->input('current_index')) &&
                $request->input('active') === 0
                    ? 'lte:last_index'
                    : ''
            ],
            'first_index' => [
                'nullable',
                'required_if:active,0',
                'string',
                'max:250',
            ],
            'last_index' => [
                'nullable',
                'required_if:active,0',
                'string',
                'max:250',
            ],
        ];

        if ($request->input('type') === self::TYPE_DATE) {
            $rules['current_index'] = [
                'date_format:d/m/Y',
                $request->input('active') === 0 ? 'before_or_equal:last_index' : '',
                function($attribute, $value, $fail) use($request) {
                    $request->merge([$attribute => Carbon::createFromFormat('!d/m/Y', $value)]);
                }
            ];

            $rules['first_index'] = $rules['last_index'] = [
                'nullable',
                'required_if:active,0',
                'date_format:d/m/Y',
                function($attribute, $value, $fail) use($request) {
                    $request->merge([$attribute => Carbon::createFromFormat('!d/m/Y', $value)]);
                }
            ];
        }

        return Validator::make($request->all(), $rules);
    }

    public function scraperObject()
    {
        if ($this->scraperObject === null) {
            if (!$this->scraper) {
                $this->scraperObject = new BaseScraper($this);
            } elseif (array_key_exists($this->scraper, $this->getScrapers())) {
                $className = '\App\Scrapers\\' . $this->scraper;
                if (!class_exists($className)) {
                    throw new InvalidArgumentException(__(
                        '#:id as an non-existent adapter: :class',
                        ['id' => $this->id, 'class' => $this->scraper]
                    ));
                }
                $this->scraperObject = new $className($this);
            } else {
                throw new InvalidArgumentException(__(
                    '#:id as an non-existent adapter: :class',
                    ['id' => $this->id, 'class' => $this->scraper]
                ));
            }
        }
        return $this->scraperObject;
    }

    public function getScrapers()
    {
        if ($this->installedScrapers === null) {
            foreach (glob(__DIR__ . '/Scrapers/*.php') as $filename) {
                $this->installedScrapers[pathinfo($filename, PATHINFO_FILENAME)] = $filename;
            }
        }
        return $this->installedScrapers;
    }

    public function getUserAgents()
    {
        return $this->userAgents;
    }

    public function getTypes()
    {
        return [
            self::TYPE_DATE => 'Date',
            self::TYPE_ID => 'ID',
        ];
    }

    public function indexUrl($index, $protocol = null)
    {
        $index = $this->index($index);
        return url(
            'comic.view',
            [
                'comic' => $this,
                'index' => $this->type === self::TYPE_DATE
                    ? $index->toDateTime()->format('d-m-Y')
                    : $index
            ]
        );
    }

    public function scrapeUrl($index)
    {
        $url = $this->scrape_url;
        $baseUrlScheme = parse_url(
            $this->base_url ?: $this->homepage,
            PHP_URL_SCHEME
        ) ?: 'http';
        $baseUrlHost = parse_url(
            $this->base_url ?: $this->homepage,
            PHP_URL_HOST
        );
        $index = $this->index($index)->format($this->index_format);

        preg_match_all('#\{\$.[^\}]*\}#', $url, $matches);

        foreach ($matches[0] as $match) {
            $params = preg_split('#[:,]#', trim($match, '{}'));
            $operator = array_shift($params);

            if ($operator === '$value' || $operator === '$index'){
                $value = $index;
            } elseif ($operator === '$date') {
                $value = (new \DateTime)->format($params[0]);
            }

            $url = str_replace($match, $value, $url);
        }

        $urlParts = parse_url($url);
        if ($urlParts) {
            $host = null;
            if (!isset($urlParts['scheme']) && !isset($urlParts['host'])) {
                $host = $baseUrlScheme . '://' . $baseUrlHost . '/';
            } elseif (!isset($urlParts['scheme'])) {
                $host = $baseUrlScheme . '://';
            }

            if ($host) {
                $url = $host . ltrim($url, '/');
            }
        }

        return $url;
    }

    public function getCurrentIndexValue()
    {
        return $this->index($this->current_index);
    }

    public function getLastIndexValue()
    {
        return $this->index($this->last_index);
    }

    public function getFirstIndexValue()
    {
        return $this->index($this->first_index);
    }

    public function getLatestIndexValue()
    {
        return $this->getCurrentIndexValue() ?? $this->getLastIndexValue();
    }

    public function index($index, $format = null, $toString = false)
    {
        $format = $format ?: $this->index_format;
        if (
            $this->type === self::TYPE_DATE &&
            !$index instanceof Carbon
        ) {
            if (!$index) {
                return null;
            } elseif (
                !Validator::make(['date' => $index], [
                    'date' => [
                        'required',
                        'date_format:' . $format,
                    ]
                ])->fails()
            ) {
                $index = new Carbon(strtotime($index));
                if ($toString) {
                    //$index = $index->toDateTime();
                }
            } else {
                throw new InvalidArgumentException(__(
                    'The index :index is not a valid date',
                    ['index' => $index]
                ));
            }
        } elseif ($this->type === self::TYPE_ID) {
            // Return a string since this is the standard for non-int ids as well
            $index = (String)$index;
        }
        return $index;
    }

    public function isIndexOutOfRange($index)
    {
        $requestedIndex = $this->index($index);
        $currentIndex = $this->index($this->current_index);

        if (
            $this->type === self::TYPE_ID &&
            !$this->isIndexInt($currentIndex)
        ) {
            // Don't run for string indices
            return false;
        }

        $firstIndex = $this->index($this->first_index);
        $lastIndex = $this->index($this->last_index);

        if ($this->active) {
            if (
                $this->type === self::TYPE_DATE &&
                (
                    $requestedIndex->toDateTime()->getTimestamp() > $currentIndex->toDateTime()->getTimestamp() ||
                    (
                        $firstIndex &&
                        $requestedIndex->toDateTime()->getTimestamp() < $firstIndex->toDateTime()->getTimestamp()
                    )
                )
            ) {
                return true;
            } elseif (
                $this->type === self::TYPE_ID &&
                $this->isIndexInt($currentIndex) &&
                (
                    $requestedIndex > $currentIndex ||
                    (
                        $firstIndex &&
                        $requestedIndex < $firstIndex
                    ) ||
                    $requestedIndex <= 0
                )
            ) {
                return true;
            }
        } else {
            if (
                $this->type === self::TYPE_DATE &&
                (
                    $requestedIndex->toDateTime()->getTimestamp() > $lastIndex->toDateTime()->getTimestamp() ||
                    $requestedIndex->toDateTime()->getTimestamp() < $firstIndex->toDateTime()->getTimestamp()
                )
            ) {
                return true;
            } elseif (
                $this->type === self::TYPE_ID &&
                $this->isIndexInt($index) &&
                (
                    $requestedIndex < $firstIndex ||
                    $requestedIndex > $lastIndex

                )
            ) {
                return true;
            }
        }
        return false;
    }

    public function isIndexInt($value)
    {
        if (
            preg_match('#^([+-]?[1-9]\d*|0)$#', $value) &&
            (int)$value < PHP_INT_MAX
        ) {
            return true;
        }
        return false;
    }

    public function current()
    {
        return $this->findStrip($this->getCurrentIndexValue());
    }

    /**
     * @param \App\ComicStrip $strip
     * @param array           $data
     *
     * @return \App\ComicStrip|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function previous(ComicStrip $strip, array $data = [])
    {
        if ($strip->previous) {
            return $this->findStrip($strip->previous, $data);
        } elseif ($this->nav_previous_dom_path) {
            // Try and re-download and see if there is a previous now
            if (
                $this->scrapeStrip($strip) &&
                $strip->previous &&
                $strip->save()
            ) {
                // If we have a previous now then let's get that
                $strip = $this->findStrip($strip->previous, $data);
                return $strip;
            }
            return null;
        }

        // Else we will ty and guess it
        $index = $this->index($strip->index);

        $previousIndex = null;
        if ($this->type === self::TYPE_DATE) {
            $previousIndex = new UTCDateTime(
                $index
                    ->toDateTime()
                    ->modify('-' . ($this->index_step ?: '1 day'))
                    ->getTimestamp() * 1000
            );
        } elseif ($this->type === self::TYPE_ID && $this->isIndexInt($index)) {
            $previousIndex = $index - ($this->index_step ?: 1);
        }

        if (
            !$previousIndex ||
            $this->isIndexOutOfRange($previousIndex)
        ) {
            return null;
        }

        $strip = $this->findStrip($this->index($previousIndex), $data);

        if (!$strip) {
            // As a last resort, to try and compensate for
            // odd schedules, do we have any previously?
            $strip = ComicStrip::query()
                ->where('comic_id', $this->_id)
                ->where('index', '<', $index)
                ->orderBy('index', 'ASC')
                ->first();
        }
        return $strip;
    }

    public function next(ComicStrip $strip, $scrape = false, array $data = [])
    {
        if ($strip->next) {
            return $this->findStrip($strip->next, $data);
        } elseif ($this->nav_next_dom_path) {
            if (
                $scrape &&
                $this->scrapeStrip($strip) &&
                $strip->next &&
                $strip->save()
            ) {
                // If we have a next now then let's get that
                $strip = $this->findStrip($strip->next, $data);
                return $strip;
            }
            return null;
        }

        // Else we will try and guess it
        $index = $this->index($strip->index);

        $nextIndex = null;
        if ($this->type === self::TYPE_DATE) {
            $nextIndex = new UTCDateTime(
                $index
                    ->toDateTime()
                    ->modify("+" . ($this->index_step ?: '1 day'))
                    ->getTimestamp() * 1000
            );
        } elseif ($this->type === self::TYPE_ID && $this->isIndexInt($index)) {
            $nextIndex = $index + ($this->index_step ?: 1);
        }

        if (
            !$nextIndex ||
            (!$scrape && $this->isIndexOutOfRange($nextIndex))
        ) {
            return null;
        }

        $strip = $this->findStrip($this->index($nextIndex), $data);

        if (!$strip) {
            // As a last resort, to try and compensate for
            // odd schedules, do we have any next?
            $strip = ComicStrip::query()
                ->where('comic_id', $this->_id)
                ->where('index', '>', $index)
                ->orderBy('index', 'DESC')
                ->first();
        }
        return $strip;
    }

    public function findStrip($index, array $data = [], $scrape = true)
    {
        $index = $this->index($index);

        $model = ComicStrip::query()
            ->where('comic_id', $this->_id)
            ->where('index', $index)
            ->first();

        if ($model) {
            return $model;
        } elseif ($scrape) {
            if (!$model) {
                $model = ComicStrip::create([
                    'comic_id' => $this->_id,
                    'index' => $index,
                ]);

                foreach ($data as $k => $v) {
                    $model->$k = $v;
                }
            }

            if ($this->scrapeStrip($model) && $model->save()) {
                return $model;
            }
        }

        return null;
    }

    public function scrapeStrip(&$model, $url = null)
    {
        return $this->scraperObject()->scrapeStrip($model, $url = null);
    }

    /**
     * Used specifically by the scraper to get new strips
     * @param bool $force
     * @return array|\common\models\ComicStrip|null|\yii\mongodb\ActiveRecord
     * @throws \Exception
     */
    public function scrapeCron($force = false)
    {
        if (!$this->live) {
            return $this->addScrapeError(
                ':title(:id) is marked as not live',
                [
                    'title' => $this->title,
                    'id' => (String)$this->_id,
                ]
            );
        }

        $currentStrip = $this->current();
        if (!$currentStrip) {
            return $this->addScrapeError(
                ':title(:id) could not find any strip for the index :index',
                [
                    'title' => $this->title,
                    'id' => (String)$this->_id,
                    'index' => $this->current_index
                ]
            );
        }

        $timeToday = (new \DateTime('today'))->getTimestamp();

        do {
            $has_next = false;

            if (
                !$this->active &&
                (
                    (
                        $this->type === self::TYPE_DATE &&
                        $this->index($currentStrip->index)->toDateTime()->getTimestamp() === $this->getLastIndexValue()->toDateTime()->getTimestamp()
                    ) || (
                        $this->type === self::TYPE_ID &&
                        $this->index($currentStrip->index) === $this->getLastIndexValue()
                    )
                )
            ) {
                // We rotate the archive going back to first_index
                $strip = $this->findStrip($this->index($this->first_index));
            } elseif (
                $currentStrip->date instanceof UTCDateTime &&
                $currentStrip->date->toDateTime()->getTimestamp() === $timeToday &&
                (!$this->active || $this->classic_edition)
            ) {
                $strip = $currentStrip;
            } else {
                $strip = $this->next(
                    $currentStrip,
                    true,
                    ['date' => new UTCDateTime($timeToday * 1000)]
                );
            }

            if ($strip) {
                $this->current_index = $this->index($strip->index);
                $this->last_checked = new UTCDateTime($timeToday * 1000);
                if (!$this->save()) {
                    return $this->addScrapeError(
                        ':title(:id) Could not save last checked and current_index for :id',
                        [
                            'title' => $this->title,
                            'id' => (String)$this->_id
                        ]
                    );
                }
            } else {
                $this->addScrapeError(
                    ':title(:id) could not find next from :url',
                    [
                        'title' => $this->title,
                        'id' => (String)$this->_id,
                        'url' => $this->scrapeUrl($currentStrip->index)
                    ]
                );
            }

            if (
                $strip &&
                $this->active &&
                !$this->classic_edition &&
                ($strip->next || $force)
            ) {
                $currentStrip = $strip;
                $has_next = true;
            }
        } while($has_next);
    }

    public function getScrapeDom(&$url, $ignoreErrors = false)
    {
        try {
            $res = (new Client)->request(
                'GET',
                $url,
                [
                    'headers' => [
                        'User-Agent' => $this->scraper_user_agent ?: $this->userAgents['Chrome User']
                    ],
                    'on_stats' => function (TransferStats $stats) use (&$url) {
                        $url = $stats->getEffectiveUri();
                    }
                ]
            );
        } catch (RequestException $e) {
            // Log the exception
            return $this->addScrapeError(
                ':title(:id) returned :response for :url',
                [
                    'title' => $this->title,
                    'id' => (String)$this->_id,
                    'response' => $e instanceof RequestException && $e->hasResponse()
                        ? $e->getResponse()->getStatusCode()
                        : $e->getMessage(),
                    'url' => $url
                ],
                $ignoreErrors
            );
        }

        $url = $url->__toString();

        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHtml($res->getBody());
        libxml_clear_errors();
        $el = new \DOMXPath($doc);
        return $el;
    }

    public function addScrapeError($message, $params = [], $ignore = false)
    {
        if (!$ignore) {
            $message = __($message, $params);
            $this->scrapeErrors[] = $message;
            //Yii::warning($message, 'comic\\' . (String)$this->_id);
        }
        return false;
    }

    public function addScrapeWarning($message, $params = [], $ignore = false)
    {
        if (!$ignore) {
            $message = __($message, $params);
            //Yii::warning($message, 'comic\\' . (String)$this->_id);
        }
        return false;
    }

    public function getScrapeErrors()
    {
        return $this->scrapeErrors;
    }

    public function clearScrapeErrors()
    {
        $this->scrapeErrors = [];
    }

    public function indexExist($index)
    {
        try {
            $res = (new Client)->request(
                'GET',
                $this->scrapeUrl($index),
                [
                    'headers' => [
                        'User-Agent' => $this->scraper_user_agent ?: $this->userAgents['Chrome User']
                    ]
                ]
            );
        } catch (ClientException $e) {
            return false;
        }
        return true;
    }

    public static function renderStripImage($id)
    {
        if (($pos = strpos($id, '_')) !== false) {
            $parts = explode('_', $id);
            $id = $parts[0];
            $index = $parts[1];
        }

        $model = ComicStrip::findOrFail(new ObjectId($id));
        // TODO dunno
    }
}
