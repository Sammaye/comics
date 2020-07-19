<?php

namespace App\Http\Controllers;

use App\Comic;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiComicController extends Controller
{
    public function getNames(Request $request)
    {
        $comics = Comic::query()
            ->where('live', 1)
            ->orderBy('title', 'ASC')
            ->get()
            ->map(function ($c) {
                return collect($c->toArray())
                    ->only(['_id', 'title'])
                    ->all();
            })
            ->toArray();

        return response()
            ->json([
                'success' => true,
                'comics' => $comics,
            ]);
    }

    public function get(Request $request, $id = null, $index = null)
    {
        $comic = Comic::query()
            ->select([
                '_id',
                'abstract',
                'active',
                'live',
                'author',
                'author_homepage',
                'base_url',
                'current_index',
                'description',
                'first_index',
                'last_index',
                'slug',
                'title',
                'homepage',
                'type',
            ])
            ->where('live', 1)
            ->where('_id', $id)
            ->firstOrFail();

        $current = $comic->findStrip(
            $comic->index($index ?: $comic->current_index, config('app.inputDateFormat')),
            [],
            false
        );
        $previous = null;
        $next = null;

        if ($current) {
            $img_src = [];

            if (is_array($current->img)) {
                foreach ($current->img as $k => $img) {
                    $img_url = route('comic.image', ['comicStrip' => $current, 'index' => $k]);
                    $img_file_name = '/tmp/' . md5(Str::random(40));
                    file_put_contents($img_file_name, $current->img->getData());
                    [$width, $height] = getimagesize($img_file_name);
                    $img_src[] = [
                        'width' => $width,
                        'height' => $height,
                        'src' => $img_url,
                    ];
                }
            } elseif ($current->img) {
                $img_url = route('comic.image', ['comicStrip' => $current]);
                $img_file_name = '/tmp/' . md5(Str::random(40));
                file_put_contents($img_file_name, $current->img->getData());
                [$width, $height] = getimagesize($img_file_name);
                $img_src[] = [
                    'width' => $width,
                    'height' => $height,
                    'src' => $img_url,
                ];
            }

            $current->img_src = $img_src;

            $previous = $comic->previous($current, [], false);
            $next = $comic->next($current, false);
        }

        $comic_a = $comic->toArray();

        $comic_a['current_index'] = $comic->current_index instanceof Carbon
            ? $comic->current_index->format(config('app.inputDateFormat'))
            : $comic->current_index;
        $comic_a['first_index'] = $comic->first_index instanceof Carbon
            ? $comic->first_index->format(config('app.inputDateFormat'))
            : $comic->first_index;
        $comic_a['last_index'] = $comic->last_index instanceof Carbon
            ? $comic->last_index->format(config('app.inputDateFormat'))
            : $comic->last_index;

        return response()
            ->json([
                'success' => true,
                'comic' => array_merge($comic_a, [
                    'strip' => $current ? [
                        '_id' => $current->_id->__toString(),
                        'comic_id' => $current->comic_id->__toString(),
                        'created_at' => $current->created_at,
                        'updated_at' => $current->updated_at,
                        'date' => $current->date,
                        'index' => $current->index instanceof Carbon
                            ? $current->index->format(config('app.inputDateFormat'))
                            : $current->index,
                        'next' => $current->next instanceof Carbon
                            ? $current->next->format(config('app.inputDateFormat'))
                            : $current->next,
                        'previous' => $current->previous instanceof Carbon
                            ? $current->previous->format(config('app.inputDateFormat'))
                            : $current->previous,
                        'skip' => $current->skip,
                        'url' => $current->url,
                        'img_src' => $current->img_src,
                    ] : null,
                    'next' => $next ? [
                        '_id' => $next->_id->__toString(),
                        'comic_id' => $next->comic_id->__toString(),
                        'created_at' => $next->created_at,
                        'updated_at' => $next->updated_at,
                        'date' => $next->date,
                        'index' => $next->index instanceof Carbon
                            ? $next->index->format(config('app.inputDateFormat'))
                            : $next->index,
                        'next' => $next->next instanceof Carbon
                            ? $next->next->format(config('app.inputDateFormat'))
                            : $next->next,
                        'previous' => $next->previous instanceof Carbon
                            ? $next->previous->format(config('app.inputDateFormat'))
                            : $next->previous,
                        'skip' => $next->skip,
                        'url' => $next->url,
                        'img_src' => $next->img_src,
                    ] : null,
                    'previous' => $previous ? [
                        '_id' => $previous->_id->__toString(),
                        'comic_id' => $previous->comic_id->__toString(),
                        'created_at' => $previous->created_at,
                        'updated_at' => $previous->updated_at,
                        'date' => $previous->date,
                        'index' => $previous->index instanceof Carbon
                            ? $previous->index->format(config('app.inputDateFormat'))
                            : $previous->index,
                        'next' => $previous->next instanceof Carbon
                            ? $previous->next->format(config('app.inputDateFormat'))
                            : $previous->next,
                        'previous' => $previous->previous instanceof Carbon
                            ? $previous->previous->format(config('app.inputDateFormat'))
                            : $previous->previous,
                        'skip' => $previous->skip,
                        'url' => $previous->url,
                        'img_src' => $previous->img_src,
                    ] : null,
                ]),
            ]);
    }

    public function subscribe(Request $request, Comic $comic)
    {
        if ($request->user()->addComic($comic->_id)) {
            return response()
                ->json([
                    'success' => true,
                    'comic_id' => $comic->_id->__toString(),
                    'message' => __(
                        'You subscribed to :title',
                        ['title' => $comic->title]
                    ),
                ]);
        }

        return response()
            ->json([
                'success' => false,
                'comic_id' => $comic->_id->__toString(),
                'message' => __('Unknown Error'),
            ]);
    }

    public function unsubscribe(Request $request, Comic $comic)
    {
        if ($request->user()->removeComic($comic->_id)) {
            return response()
                ->json([
                    'success' => true,
                    'comic_id' => $comic->_id->__toString(),
                    'message' => __(
                        'You unsubscribed from :title',
                        ['title' => $comic->title]
                    ),
                ]);
        }

        return response()
            ->json([
                'success' => false,
                'comic_id' => $comic->_id->__toString(),
                'message' => __('Unknown Error'),
            ]);
    }
}
