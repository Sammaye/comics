<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
