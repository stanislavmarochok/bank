<?php

return [
    'default' => 'default',
    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'ZZZ Bank API Docs',
            ],

            'routes' => [
                'api' => 'api/documentation',
            ],
            'paths' => [
                'docs_json' => 'api-docs.json',
                'docs_yaml' => 'api-docs.yaml',
                'format_to_use_for_docs' => env('L5_FORMAT_TO_USE_FOR_DOCS', 'json'),
                'annotations' => [
                    base_path('app'),
                ],

            ],
        ],
    ],
    'defaults' => [
        'routes' => [
            'docs'            => 'docs',
            'oauth2_callback' => 'api/oauth2-callback',
        ],

        'paths' => [
            'docs'                   => storage_path('api-docs'),
            'views'                  => base_path('resources/views/vendor/l5-swagger'),
            'base'                   => env('L5_SWAGGER_BASE_PATH', null),
            'swagger_ui_assets_path' => env('L5_SWAGGER_UI_ASSETS_PATH', 'vendor/swagger-api/swagger-ui/dist/'),
            'excludes'               => [],
        ],

        'generate_always'       => env('L5_SWAGGER_GENERATE_ALWAYS', false),
        'generate_yaml_copy'    => env('L5_SWAGGER_GENERATE_YAML_COPY', false),
        'proxy'                 => false,
        'additional_config_url' => null,
        'operations_sort'       => env('L5_SWAGGER_OPERATIONS_SORT', null),
        'validator_url'         => null,
    ],
];
