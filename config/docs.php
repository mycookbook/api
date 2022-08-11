<?php

return [
    'api' => [
        'users' => [
            'register' => [
                'method' => 'POST',
                'endpoint' => 'api/v1/auth/register',
            ],
            'login' => [
                'method' => 'POST',
                'endpoint' => 'api/v1/auth/login',
            ],
        ],
        'cookbooks' => [
            'routes' => [
                'view_all' => [
                    'GET' => 'api/v1/cookbooks',
                ],
                'get_one' => [
                    'GET' => 'api/v1/cookbooks/:id',
                ],
                'create_one' => [
                    'POST' => 'api/v1/cookbooks',
                ],
                'partial_update_one' => [
                    'PATCH' => 'api/v1/cookbooks/:id',
                ],
                'full_update_one' => [
                    'PUT' => 'api/v1/cookbooks/:id',
                ],
            ],
        ],
    ],
];
