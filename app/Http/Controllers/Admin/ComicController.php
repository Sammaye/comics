<?php

namespace App\Http\Controllers\Admin;

use App\Comic;
use App\ComicStrip;
use danielme85\LaravelLogToDB\LogToDB;
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
            if (!preg_match('/^comic-/', $k)) {
                continue;
            }

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

        $logs = LogToDB::model(
            null,
            'mongodb',
            config('logging.channels.scraper.collection')
        )->newModelQuery();

        foreach (array_filter($request->input()) as $k => $v) {
            if (!preg_match('/^log-/', $k)) {
                continue;
            }

            $column_name = str_replace('log-', '', $k);
            if ($k === 'log-sort') {
                $logs->orderBy(
                    trim($v, '-'),
                    strpos($v, '-') === 0 ? 'DESC' : 'ASC'
                );
            } elseif ($k !== 'log-page') {
                if ($column_name === 'id') {
                    $logs->where('_id', '=', new ObjectId($v));
                } else {
                    $logs->where(
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
                'logs' => $logs,
            ]);
    }

    private function filterAdminTableModel($request, $query, $filterAllowedFields = []) {
        $currentPage = (int)$request->input('currentPage') ?: 1;
        $perPage = (int)$request->input('perPage') ?: 20;
        $sortField = $request->input('sortBy');
        $sortDir = $request->input('sortDesc', false) ? 'desc' : 'asc';

        $filter = trim($request->input('filter'));
        $filterOn = $request->input('filterOn');
        $filterOn = is_array($filterOn) && count($filterOn)
            ? array_intersect($filterOn, $filterAllowedFields)
            : $filterAllowedFields;

        if ($filter) {
            foreach ($filterOn as $field) {
                $query->where($field, $filter);
            }
        }

        if ($sortField) {
            $query->orderBy($sortField, $sortDir);
        } else {
            $query->orderBy('_id', 'asc');
        }

        $total = $query->count();

        $query->skip($perPage * ($currentPage - 1));
        $query->limit($perPage);

        return [$query->get(), $total];
    }

    public function adminTableData(Request $request)
    {
        [$models, $total] = $this->filterAdminTableModel($request, Comic::query(), ['_id', 'title', 'abstract']);

        $items = $models->map(function ($item, $key) {
            return [
                '_id' => $item->_id->__toString(),
                'title' => $item->title,
                'abstract' => $item->abstract,
                'strips' => $item->strips->count(),
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $item->updated_at->format('Y-m-d H:i:s'),
                'edit_url' => route('admin.comic.edit', ['comic' => $item->_id]),
                'delete_url' => route('admin.comic.adminTableDelete', [], false),
            ];
        });

        return response()->json([
            'success' => true,
            'items' => $items,
            'items_count' => $total,
        ]);
    }

    public function adminTableDelete(Request $request)
    {
        $comic = Comic::query()->where('_id', $request->input('id'))->firstOrFail();

        $comic->delete();

        return response()
            ->json([
                'success' => true,
                'id' => $comic->_id->__toString(),
            ]);
    }

    public function logsAdminTableData(Request $request)
    {
        [$models, $total] = $this->filterAdminTableModel($request, LogToDB::model(
            null,
            'mongodb',
            config('logging.channels.scraper.collection')
        )->newModelQuery(), ['_id', 'message', 'created_at']);

        $items = $models->map(function ($item, $key) {
            return [
                '_id' => $item->_id->__toString(),
                'message' => $item->message,
                'trace' => $item->trace,
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'items' => $items,
            'items_count' => $total,
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
     * @param \Illuminate\Http\Request $request
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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Comic $comic)
    {
        $comicStrips = ComicStrip::query()
            ->where('comic_id', $comic->_id)
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
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comic $comic)
    {
        $request->merge(collect($comic->toArray())->merge($request->all())->toArray());
        $validator = $comic->getValidator($request);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.comic.edit', ['comic' => $comic])
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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comic $comic)
    {
        $comic->delete();

        Flash::success(__('Comic Deleted'));
        return redirect()
            ->route('admin.comic.index');
    }
}
