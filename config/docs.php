<?php

return [
    'api' => [
        'users' => [
            'signup' => [
                'method' => 'POST',
                'endpoint' => 'api/v1/auth/signup'
            ],
            'signin' => [
                'method' => 'POST',
                'endpoint' => 'api/v1/auth/signin'
            ]
        ]
    ],
];