<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use MongoDB\BSON\ObjectId;
use sammaye\Flash\Support\Flash;
use sammaye\Permission\Mongodb\Permission;
use sammaye\Permission\Mongodb\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::query();

        foreach (array_filter($request->input()) as $k => $v) {
            $column_name = str_replace('user-', '', $k);
            if ($k === 'user-sort') {
                $users->orderBy(
                    trim($v, '-'),
                    strpos($v, '-') === 0 ? 'DESC' : 'ASC'
                );
            } elseif (
                Schema::hasColumn((new User)->getTable(), $column_name) &&
                $k !== 'user-page'
            ) {
                if ($column_name === 'id') {
                    $users->where('_id', new ObjectId($v));
                } else {
                    $users->where(
                        $column_name,
                        'regexp',
                        '/' . $v . '/i'
                    );
                }
            }
        }

        return view('admin.user.index')
            ->with([
                'users' => $users,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => [
                'required',
                'string',
                'regex:/^[0-9A-Za-z_]+$/',
                'max:255',
                'unique:users',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
            ],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.user.create')
                ->withInput()
                ->withErrors($validator);
        }

        $data = $validator->validated();

        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Flash::success(__('User :id Created', ['id' => $user->id]));

        return redirect()
            ->route('admin.user.edit', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('admin.user.edit')
            ->with([
                'model' => $user,
                'roles' => Role::all(),
                'permissions' => Permission::all(),
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param \App\User                 $user
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if ($request->input('action') === 'unblock') {
            $user->forceFill([
                'blocked_at' => null,
            ])->save();

            Flash::success(__('User Unblocked'));

            return redirect()
                ->route('admin.user.edit', ['user' => $user]);
        }
        if ($request->input('action') === 'block') {
            $user->forceFill([
                'blocked_at' => Carbon::now(),
            ])->save();

            Flash::success(__('User Blocked'));

            return redirect()
                ->route('admin.user.edit', ['user' => $user]);
        }
        if ($request->input('action') === 'verify_email') {
            $user->forceFill([
                'email_verified_at' => Carbon::now(),
            ])->save();

            Flash::success(__('User E-Mail Address Verified'));

            return redirect()
                ->route('admin.user.edit', ['user' => $user]);
        }

        $validator = Validator::make($request->all(), [
            'username' => [
                'required',
                'string',
                'regex:/^[0-9A-Za-z_]+$/',
                'max:255',
                Rule::unique('users')->ignore($user->id, '_id')
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->_id, '_id')
            ],
            'password' => ['sometimes', 'string', 'min:6'],
            'role' => ['sometimes', 'array'],
            'role.*' => [
                'sometimes',
                Rule::in(
                    Role::all()->pluck('id')->toArray()
                )
            ],
            'permission' => ['sometimes', 'array'],
            'permission.*' => [
                'sometimes',
                Rule::in(
                    Permission::all()->pluck('id')->toArray()
                )
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.user.edit', ['user' => $user])
                ->withInput()
                ->withErrors($validator);
        }

        $data = $validator->validated();

        if (isset($data['role'])) {
            $user->roles()->sync($data['role']);
        }
        if (isset($data['permission'])) {
            $user->permissions()->sync($data['permission']);
        }
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        unset($data['role'], $data['permission'], $data['password']);

        $user->forceFill($data)->save();

        Flash::success(__('User :id Updated', ['id' => $user->id]));

        if ($user->id === $request->user()->id) {
            Auth::login($user, true);
        }

        return redirect()
            ->route('admin.user.edit', ['user' => $user]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\User $user
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(User $user)
    {
        $user->delete();

        Flash::success(__('User Deleted'));
        return redirect()
            ->route('admin.user.index');
    }
}
