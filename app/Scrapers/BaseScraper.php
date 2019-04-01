<?php

namespace App\Scrapers;

use App\Comic;
use MongoDB\BSON\Binary;

Class BaseScraper
{
    protected $comic;

    public function __construct(Comic $comic)
    {
        $this->comic = $comic;
    }

    public function scrapeStrip(&$model, $url = null)
    {
        $imageUrl = null;

        $baseUrl = rtrim($this->comic->base_url ?: $this->comic->scrape_url, '/');
        $baseUrlScheme = parse_url($baseUrl, PHP_URL_SCHEME) ?: 'http';
        $baseUrlHost = parse_url($baseUrl, PHP_URL_HOST);
        if (!$baseUrlHost) {
            // As a last resort we will check the homepage link
            $baseUrl = rtrim($this->comic->homepage, '/');
            $baseUrlScheme = parse_url($baseUrl, PHP_URL_SCHEME) ?: 'http';
            $baseUrlHost = parse_url($baseUrl, PHP_URL_HOST);
        }

        $url = $url ?: $this->comic->scrapeUrl($model->index);

        $domPath = preg_split('#>>#', $this->comic->image_dom_path);
        foreach ($domPath as $k => $v) {
            $domPath[$k] = preg_split('#\|\|#', $v);
        }

        // TODO handle more complex DOM paths, such as ones which are multi-page
        $domPath = end($domPath);

        $dom = $this->comic->getScrapeDom($url);

        if (!$dom) {
            return $this->comic->addScrapeError(
                ':title(:id) could not instantiate DOMDocument Object for :url',
                [
                    'title' => $this->comic->title,
                    'id' => (String)$this->comic->_id,
                    'url' => $url,
                ]
            );
        }

        foreach ($domPath as $path) {
            $elements = $dom->query($path);

            if ($elements->length <= 0) {
                continue;
            }

            foreach ($elements as $element) {
                $imageUrl = $element->getAttribute('src');
                break;
            }

            if ($imageUrl) {
                break;
            }
        }

        if (!$imageUrl) {
            $this->comic->addScrapeWarning(
                ':title(:id) could not find img with src for :url',
                [
                    'title' => $this->comic->title,
                    'id' => (String)$this->comic->_id,
                    'url' => $url
                ]
            );
        } else {
            $imageUrlParts = parse_url($imageUrl);
            if ($imageUrlParts) {
                $imageUrlHost = null;
                if (!isset($imageUrlParts['scheme']) && !isset($imageUrlParts['host'])) {
                    $imageUrlHost = $baseUrlScheme . '://' . $baseUrlHost . '/';
                } elseif (!isset($imageUrlParts['scheme'])) {
                    $imageUrlHost = $baseUrlScheme . '://';
                }

                if ($imageUrlHost) {
                    $imageUrl = $imageUrlHost . ltrim($imageUrl, '/');
                }
            }
            $model->image_url = $imageUrl;
        }

        if ($this->comic->nav_next_dom_path && $this->comic->nav_previous_dom_path) {
            $navDomElements = [
                'previous' => $dom->query($this->comic->nav_previous_dom_path),
                'next' => $dom->query($this->comic->nav_next_dom_path),
            ];
            $navUrlRegex = $this->comic->nav_url_regex
                ?
                : preg_quote(
                    preg_replace('#\{\$value\}|\{\$index\}#', '', $baseUrl),
                    '#'
                ) . '(?<index>[A-Za-z0-9-_]+)';

            foreach ($navDomElements as $k => $element) {
                $matches = [];

                if ($element->length <= 0) {
                    continue;
                }

                $navLinkUrl = $element[0]->getAttribute('href');
                preg_match_all("#$navUrlRegex#", $navLinkUrl, $matches);

                if (!isset($matches['index'][0])) {
                    $this->comic->addScrapeWarning(
                        ':title(:id) could not parse navigation URL :url for the field :field',
                        [
                            'title' => $this->comic->title,
                            'id' => (String)$this->comic->_id,
                            'url' => $navLinkUrl,
                            'field' => $k === 'previous' ? 'nav_previous_dom_path' : 'nav_next_dom_path',
                        ]
                    );
                    continue;
                }
                $model->$k = $this->comic->index($matches['index'][0], $this->comic->index_format);
            }
        }

        $model->url = $url;

        try {
            if ($model->image_url) {
                // Sometimes people like to put crappy special characters into file names
                if (pathinfo($model->image_url, PATHINFO_EXTENSION)) {
                    $filename = pathinfo($model->image_url, PATHINFO_FILENAME);
                    $encodedFilename = rawurlencode($filename);
                    $imageUrl = str_replace($filename, $encodedFilename, $model->image_url);
                }

                if (($binary = file_get_contents($imageUrl))) {
                    $model->image_md5 = md5($binary);
                    $model->img = new Binary($binary, Binary::TYPE_GENERIC);
                    $model->skip = 0;
                    return true;
                }
            }

            throw new \Exception;
        } catch (\Exception $e) {
            // the file probably had a problem beyond our control
            // As such define this as a skip strip since I cannot store it
            $model->skip = 1;
            return true;
        }
    }
}
