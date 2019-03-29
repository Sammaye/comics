<?php

use App\User;
use sammaye\Permission\Mongodb\Permission;
use sammaye\Permission\Mongodb\Role;

return [
    'permissions' => [
        'root',
        'admin' => [
            'admin-user',
            'admin-comic',
        ],
    ],

    'user' => User::class,
    'permission' => Permission::class,
    'role' => Role::class,
];
