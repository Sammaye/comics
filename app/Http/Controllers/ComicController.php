<?php

namespace App\Http\Controllers;

use App\Comic;
use App\ComicStrip;
use App\Mail\comicRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;


class ComicController extends Controller
{

    public function view(Request $request, $id = null, $index = null)
    {
        $index = $index ?? $request->query('index');

        View::share(
            'comicSelectorOptions',
            Comic::query()
                ->where('live', 1)
                ->orderBy('title', 'ASC')
                ->get()
                ->keyBy('id')
                ->map(function ($c) {
                    return collect($c->toArray())
                        ->only(['title', 'author'])
                        ->all();
                })
                ->toArray()
        );

        $query = Comic::query()
            ->where('live', 1);

        if ($id) {
            $query->where('_id', $id);
        }

        $comic = $query->first();

        if (!$comic) {
            return response()
                ->view('comic.notFound', [], 404);
        }

        View::share('selectedComicId', (string)$comic->_id);

        $current = $comic->findStrip(
            $comic->index($index ?: $comic->current_index, 'd-m-Y'),
            [],
            false
        );

        if (!$current) {
            return response()
                ->view('comic.stripNotFound', ['model' => $comic], 404);
        }

        $current->comic = $comic;
        $previous = $comic->previous($current);
        $next = $comic->next($current);

        return view('comic.view')
            ->with([
                'model' => $comic,
                'comicStrip' => $current,
                'previousStrip' => $previous,
                'nextStrip' => $next,
                'isSubscribed' => $request->user() && $request->user()->hasComic($comic->_id),
            ]);
    }

    public function subscribe(Request $request, Comic $comic)
    {
        if ($request->user()->addComic($comic->_id)) {
            return response()
                ->json([
                    'success' => true,
                    'message' => __(
                        'You subscribed to :title',
                        ['title' => $comic->title]
                    ),
                ]);
        }

        return response()
            ->json([
                'success' => false,
                'message' => __('Unknown Error'),
            ]);
    }

    public function unsubscribe(Request $request, Comic $comic)
    {
        if ($request->user()->removeComic($comic->_id)) {
            return response()
                ->json([
                    'success' => true,
                    'message' => __(
                        'You unsubscribed from :title',
                        ['title' => $comic->title]
                    ),
                ]);
        }

        return response()
            ->json([
                'success' => false,
                'message' => __('Unknown Error'),
            ]);
    }

    public function request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:350',
            'url' => 'required|url',
            'email' =>  'required|email'
        ]);

        if ($validator->fails()) {
            return response()
                ->json([
                    'success' => false,
                    'errors' => $validator->errors()->all(),
                    'message' => __('Could not send your request because:'),
                ]);
        }

        $data = $validator->validated();

        Mail::to(config('mail.admin_email_address'))
            ->send(new comicRequest($data));

        return response()
            ->json([
                'success' => true,
                'message' => __('Request Received'),
            ]);
    }

    public function getStripImage(Request $request, ComicStrip $comicStrip, $index = null)
    {
        return response()
            ->file($comicStrip->comic->getStripImageFilePath($comicStrip))
            ->deleteFileAfterSend(true);
    }
}
