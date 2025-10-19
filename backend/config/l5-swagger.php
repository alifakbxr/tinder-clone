<?php

return [
    'default' => 'default',
    'docs' => [
        'routes' => [
            /*
             * Route for accessing api documentation interface
             */
            'api' => 'api/documentation',
            'docs' => 'docs',
            'oauth2_callback' => 'api/oauth2-callback',
            /*
             * Middleware that should be applied to all the api related routes.
             *
             * @var array
             */
            'middleware' => [
                'api' => [],
            ],
            /*
             * Route Group options
             */
            'group_options' => [],
        ],
        'paths' => [
            /*
             * Absolute path to location where parsed swagger annotations will be stored.
             */
            'docs_json' => 'api-docs.json',
            /*
             * Absolute path to location where generated swagger ui view files will be stored.
             */
            'docs_yaml' => 'api-docs.yaml',
            /*
             * Absolute path to directory containing the swagger annotations are stored.
             */
            'annotations' => base_path('app'),
            /*
             * Absolute path to storage where generated swagger files will be stored.
             */
            'generated' => storage_path('api-docs'),
            /*
             * Set this to `true` in development mode so that docs are regenerated on each request
             * Set this to `false` to disable it (faster)
             */
            'excludes' => [
                storage_path('api-docs'),
                storage_path('framework'),
                base_path('vendor'),
            ],
        ],
        'info' => [
            /*
             * The title of the application.
             */
            'title' => 'Tinder Clone API',
            /*
             * A short description of the application.
             */
            'description' => 'API documentation for the Tinder Clone application',
            /*
             * The version of the application.
             */
            'version' => '1.0.0',
            /*
             * The contact email address.
             */
            'contact' => [
                'email' => 'admin@tinder-clone.com',
            ],
            /*
             * The license of the application.
             */
            'license' => [
                'name' => 'MIT',
                'url' => 'https://opensource.org/licenses/MIT',
            ],
        ],
        'servers' => [
            [
                'url' => env('APP_URL', 'http://localhost:8000'),
                'description' => 'Laravel API Server',
            ],
        ],
        'security' => [
            /*
             * Set this to true to enable authentication
             */
            'securityDefinitions' => [
                'sanctum' => [
                    'type' => 'apiKey',
                    'description' => 'Laravel Sanctum Token',
                    'name' => 'Authorization',
                    'in' => 'header',
                ],
            ],
        ],
        'components' => [
            'schemas' => [
                'UserResource' => [
                    'type' => 'object',
                    'properties' => [
                        'id' => [
                            'type' => 'integer',
                            'description' => 'User ID'
                        ],
                        'name' => [
                            'type' => 'string',
                            'description' => 'User name'
                        ],
                        'age' => [
                            'type' => 'integer',
                            'description' => 'User age'
                        ],
                        'latitude' => [
                            'type' => 'number',
                            'format' => 'float',
                            'description' => 'User latitude coordinate'
                        ],
                        'longitude' => [
                            'type' => 'number',
                            'format' => 'float',
                            'description' => 'User longitude coordinate'
                        ],
                        'pictures' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'object',
                                'properties' => [
                                    'id' => ['type' => 'integer'],
                                    'user_id' => ['type' => 'integer'],
                                    'picture_path' => ['type' => 'string'],
                                    'created_at' => ['type' => 'string', 'format' => 'date-time'],
                                    'updated_at' => ['type' => 'string', 'format' => 'date-time']
                                ]
                            ],
                            'description' => 'User profile pictures'
                        ],
                        'created_at' => [
                            'type' => 'string',
                            'format' => 'date-time',
                            'description' => 'User creation timestamp'
                        ],
                        'updated_at' => [
                            'type' => 'string',
                            'format' => 'date-time',
                            'description' => 'User last update timestamp'
                        ]
                    ]
                ],
                'SwipeRequest' => [
                    'type' => 'object',
                    'required' => ['swiped_id', 'action'],
                    'properties' => [
                        'swiped_id' => [
                            'type' => 'integer',
                            'description' => 'ID of the user being swiped on'
                        ],
                        'action' => [
                            'type' => 'string',
                            'enum' => ['like', 'nope'],
                            'description' => 'Swipe action (like or nope)'
                        ]
                    ]
                ],
                'SwipeResponse' => [
                    'type' => 'object',
                    'properties' => [
                        'message' => [
                            'type' => 'string',
                            'example' => 'Swipe stored successfully'
                        ],
                        'swipe' => [
                            'type' => 'object',
                            'properties' => [
                                'id' => ['type' => 'integer'],
                                'swiper_id' => ['type' => 'integer'],
                                'swiped_id' => ['type' => 'integer'],
                                'action' => ['type' => 'string'],
                                'created_at' => ['type' => 'string', 'format' => 'date-time'],
                                'updated_at' => ['type' => 'string', 'format' => 'date-time']
                            ]
                        ]
                    ]
                ]
            ]
        ],
    ],
    'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', false),
    'generate_yaml_copy' => env('L5_SWAGGER_GENERATE_YAML_COPY', false),
    'proxy' => false,
    'additional_config_url' => null,
    'operations_sort' => env('L5_SWAGGER_OPERATIONS_SORT', null),
    'validator_url' => null,
];
