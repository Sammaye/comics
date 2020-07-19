<?php

namespace App\Http\Controllers;

use App\Comic;
use Illuminate\Http\Request;
use MongoDB\BSON\Regex;

class ApiUserController extends Controller
{
    public function user(Request $request)
    {
        return response()
            ->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()
            ->json([
                'success' => true,
            ]);
    }

    public function subscriptions(Request $request)
    {
        $subscriptions = collect($request->user()->comics)->keyBy('comic_id')->map(function ($sub) {
            return $sub['date']->toDateTime()->format('Y-m-d H:i:s');
        });

        $comics_query = Comic::query();

        $search = $request->get('search');
        if ($search) {
            $comics_query = $comics_query->where('title', new Regex("$search", 'i'));
        }

        $comics = $comics_query
            ->where('live', 1)
            ->orderBy('title', 'ASC')
            ->get()
            ->map(function ($c) {
                return collect($c->toArray())
                    ->only(['_id', 'title'])
                    ->all();
            })
            ->toArray();

        foreach ($comics as $k => $comic) {
            if (isset($subscriptions[$comic['_id']])) {
                $comics[$k]['subscribed'] = true;
                $comics[$k]['subscribed_date'] = $subscriptions[$comic['_id']];
            }
        }

        return response()
            ->json([
                'success' => true,
                'subscriptions' => $comics,
            ]);
    }
}
