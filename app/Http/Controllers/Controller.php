<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param $request
     * @param $query
     * @param array $filterAllowedFields
     * @return array
     */
    protected function filterAdminTableModel($request, $query, $filterAllowedFields = [])
    {
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
}
