<?php

namespace App\Http\Controllers\Admin;

use App\Comic;
use App\ComicStrip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use sammaye\Flash\Support\Flash;

class ComicStripController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, Comic $comic)
    {
        $model = new ComicStrip();
        $model->forceFill([
            'comic_id' => $comic->_id,
        ]);

        return view('admin.comicStrip.create')
            ->with([
                'model' => $model,
                'comic' => $comic,
            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $comicStrip = new ComicStrip();
        $comicStrip->forceFill([
            'comic_id' => $request->input('comic_id'),
        ]);

        if (!$comicStrip->comic) {
            Flash::error(__('Comic for Comic Strip Not Found'));
            return redirect()
                ->route('admin.comic.index');
        }

        $request->merge(collect($comicStrip->toArray())->merge($request->all())->toArray());
        $validator = $comicStrip->getValidator($request);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.comicStrip.create', ['comic' => $comicStrip->comic])
                ->withInput()
                ->withErrors($validator);
        }

        $modelData = [];
        foreach ($validator->validated() as $k => $v) {
            $modelData[$k] = $request->input($k);
        }

        $comicStrip->forceFill($modelData);

        if ($comicStrip->skip) {
            $comicStrip->url = $comicStrip->comic->scrapeUrl($comicStrip->index);
        } else {
            $comicStrip->comic->scrapeStrip($comicStrip);
            if (count($comicStrip->comic->getScrapeErrors()) > 0) {
                foreach ($comicStrip->comic->getScrapeErrors() as $error) {
                    $validator->getMessageBag()->add('url', $error);

                    return redirect()
                        ->route(
                            'admin.comicStrip.create',
                            ['comic' => $comicStrip->comic]
                        )
                        ->withInput()
                        ->withErrors($validator);
                }
            }
        }

        $comicStrip->save();

        Flash::success(__('Comic Strip Created'));
        return redirect()
            ->route('admin.comicStrip.edit', ['comicStrip' => $comicStrip]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\ComicStrip $comicStrip
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(ComicStrip $comicStrip)
    {
        return view('admin.comicStrip.edit')
            ->with([
                'model' => $comicStrip,
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\ComicStrip          $comicStrip
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ComicStrip $comicStrip)
    {
        if (!$comicStrip->comic) {
            Flash::error(__('Comic for Comic Strip Not Found'));
            return redirect()
                ->route('admin.comic.index');
        }

        $request->merge(collect($comicStrip->toArray())->merge($request->all())->toArray());
        $validator = $comicStrip->getValidator($request);

        if ($validator->fails()) {
            return redirect()
                ->route(
                    'admin.comicStrip.edit',
                    ['comicStrip' => $comicStrip]
                )
                ->withInput()
                ->withErrors($validator);
        }

        $modelData = [];
        foreach ($validator->validated() as $k => $v) {
            $modelData[$k] = $request->input($k);
        }

        $comicStrip->forceFill($modelData)->save();

        Flash::success(__('Comic Strip Updated'));
        return redirect()
            ->route('admin.comicStrip.edit', ['comicStrip' => $comicStrip]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\ComicStrip          $comicStrip
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function image(Request $request, ComicStrip $comicStrip)
    {
        $filename = uniqid('comic_strip_', true);
        Storage::disk('local')->put($filename, $comicStrip->img->getData());
        $filepath = Storage::disk('local')->path($filename);

        return response()
            ->file($filepath)
            ->deleteFileAfterSend(true);
    }

    public function refresh(Request $request, ComicStrip $comicStrip)
    {
        $comicStrip->comic->scrapeStrip($comicStrip);
        if (count($comicStrip->comic->getScrapeErrors()) > 0) {
            Flash::error(__(
                'Error: :message',
                ['message' => $comicStrip->comic->getScrapeErrors()[0]]
            ));
            return redirect()
                ->route(
                    'admin.comicStrip.edit',
                    ['comicStrip' => $comicStrip]
                );
        }

        $comicStrip->save();

        Flash::success(__('Strip refreshed'));
        return redirect()
            ->route(
                'admin.comicStrip.edit',
                ['comicStrip' => $comicStrip]
            );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\ComicStrip          $comicStrip
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Request $request, ComicStrip $comicStrip)
    {
        $comic = $comicStrip->comic;

        $comicStrip->delete();

        Flash::success(__('Comic Strip Deleted'));
        if ($comic) {
            return redirect()
                ->route('admin.comic.edit', ['comic' => $comic]);
        } else {
            return redirect()
                ->route('admin.comic.index');
        }
    }
}
