<?php
return [
    'permissions' => [
        'root',
        'admin' => [
            'admin-user',
            'admin-comics',
        ],
    ],

    'user' => \App\User::class,
    'permission' => \sammaye\Permission\Mongodb\Permission::class,
    'role' => \sammaye\Permission\Mongodb\Role::class,
];
