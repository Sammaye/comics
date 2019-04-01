<?php

namespace App\Http\Controllers\Admin;

use App\Comic;
use App\ComicStrip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use MongoDB\BSON\ObjectId;
use sammaye\Flash\Support\Flash;

class ComicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $comics = Comic::query();

        foreach (array_filter($request->input()) as $k => $v) {
            $column_name = str_replace('comic-', '', $k);
            if ($k === 'comic-sort') {
                $comics->orderBy(
                    trim($v, '-'),
                    strpos($v, '-') === 0 ? 'DESC' : 'ASC'
                );
            } elseif (
                Schema::hasColumn((new Comic)->getTable(), $column_name) &&
                $k !== 'comic-page'
            ) {
                if ($column_name === 'id') {
                    $comics->where('_id', new ObjectId($v));
                } else {
                    $comics->where(
                        $column_name,
                        'regexp',
                        '/' . $v . '/i'
                    );
                }
            }
        }

        return view('admin.comic.index')
            ->with([
                'comics' => $comics,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.comic.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $model = new Comic;
        $request->merge(collect($model->toArray())->merge($request->all())->toArray());
        $validator = $model->getValidator($request);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.comic.create')
                ->withInput()
                ->withErrors($validator);
        }

        $modelData = [];
        foreach ($validator->validated() as $k => $v) {
            $modelData[$k] = $request->input($k);
        }

        $comic = Comic::create($modelData);

        Flash::success(__('Comic Created'));
        return redirect()
            ->route('admin.comic.edit', ['comic' => $comic]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Comic $comic)
    {
        $comicStrips = ComicStrip::query()
            ->where('_id', $comic->_id)
            ->orderBy('created_at', 'DESC');

        return view('admin.comic.edit')
            ->with([
                'model' => $comic,
                'comicStrips' => $comicStrips,
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comic $comic)
    {
        $request->merge(collect($comic->toArray())->merge($request->all())->toArray());
        $validator = $comic->getValidator($request);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.comic.create')
                ->withInput()
                ->withErrors($validator);
        }

        $modelData = [];
        foreach ($validator->validated() as $k => $v) {
            $modelData[$k] = $request->input($k);
        }

        $comic->forceFill($modelData)->save();

        Flash::success(__('Comic Updated'));
        return redirect()
            ->route('admin.comic.edit', ['comic' => $comic]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comic $comic)
    {
        $comic->delete();

        return redirect()
            ->route('admin.comic.index');
    }
}
