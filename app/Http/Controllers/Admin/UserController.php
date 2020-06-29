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
        return view('admin.user.index');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminTableData(Request $request)
    {
        [$models, $total] = $this->filterAdminTableModel($request, User::query(), ['_id', 'username', 'email']);

        $items = $models->map(function ($item, $key) {
            return [
                '_id' => $item->_id->__toString(),
                'username' => $item->username,
                'email' => $item->email,
                'email_verified_at' => $item->email_verified_at
                    ? $item->email_verified_at->format('Y-m-d H:i:s')
                    : null,
                'has_verified_email' => $item->hasVerifiedEmail(),
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $item->updated_at->format('Y-m-d H:i:s'),
                'edit_url' => route('admin.user.edit', ['user' => $item->_id], false),
                'delete_url' => route('admin.user.adminTableDelete', [], false),
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
        $user = User::query()->where('_id', $request->input('id'))->firstOrFail();

        $user->delete();

        return response()
            ->json([
                'success' => true,
                'id' => $user->_id->__toString(),
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
     * @param \Illuminate\Http\Request $request
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
                'unique:user',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:user',
            ],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if ($validator->fails()) {
            return response()
                ->json([
                    'success' => false,
                    'errors' => $validator->errors()->getMessages(),
                ]);
        }

        $data = $validator->validated();

        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return response()
            ->json([
                'success' => true,
                'flash' => __('User :id Created', ['id' => $user->id]),
                'data' => $this->getUserModel($user),
            ]);
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
                'vue_model' => $this->getUserModel($user),
                'roles' => Role::all(),
                'permissions' => Permission::all(),
            ]);
    }

    private function getUserModel(User $user) {

        $data = $user->toArray() + [
            'has_verified_email' => $user->hasVerifiedEmail(),
            'is_blocked' => $user->isBlocked(),
            'action_url' => route('admin.user.update', ['user' => $user], false),
            'edit_url' => route('admin.user.edit', ['user' => $user], false),
            'delete_url' => route('admin.user.destroy', ['user' => $user], false),
            'page_title' => config('app.name', 'Laravel') . ' - ' . __('Edit User #:id', ['id' => $user->_id]),
        ];

        $roles = [];
        $user_roles = [];

        foreach (Role::all() as $role) {
            $roles[] = [
                'id' => $role->id,
                'value' => $role->id,
                'name' => $role->name,
            ];

            if ($user->hasRole($role->name)) {
                $user_roles[] = $role->id;
            }
        }

        $data['roles'] = $roles;
        $data['role'] = $user_roles;

        $permissions = [];
        $user_permissions = [];

        foreach (Permission::all() as $permission) {
            $permissions[] = [
                'id' => $permission->id,
                'value' => $permission->id,
                'name' => $permission->name,
            ];

            if ($user->hasDirectPermission($permission->name)) {
                $user_permissions[] = $permission->id;
            }
        }

        $data['permissions'] = $permissions;
        $data['permission'] = $user_permissions;

        return $data;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if ($request->input('action') === 'unblock') {
            $user->forceFill([
                'blocked_at' => null,
            ])->save();

            return response()
                ->json([
                    'success' => true,
                    'flash' => __('User Unblocked'),
                    'data' => $this->getUserModel($user),
                ]);
        }
        if ($request->input('action') === 'block') {
            $user->forceFill([
                'blocked_at' => Carbon::now(),
            ])->save();

            return response()
                ->json([
                    'success' => true,
                    'flash' => __('User Blocked'),
                    'data' => $this->getUserModel($user),
                ]);
        }
        if ($request->input('action') === 'verify_email') {
            $user->forceFill([
                'email_verified_at' => Carbon::now(),
            ])->save();

            return response()
                ->json([
                    'success' => true,
                    'flash' => __('User E-Mail Address Verified'),
                    'data' => $this->getUserModel($user),
                ]);
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
            return response()
                ->json([
                    'success' => false,
                    'errors' => $validator->errors()->getMessages(),
                ]);
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

        if ($user->id === $request->user()->id) {
            Auth::login($user, true);
        }

        return response()
            ->json([
                'success' => true,
                'flash' => __('User :id Updated', ['id' => $user->id]),
                'data' => $this->getUserModel($user),
            ]);
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

        return response()
            ->json([
                'success' => true,
                'redirect_to' => route('admin.user.index', [], false),
            ]);
    }
}
