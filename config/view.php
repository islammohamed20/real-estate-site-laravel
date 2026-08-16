<?php

return [
    'paths' => [resource_path('views')],
    'compiled' => env('VIEW_COMPILED_PATH', storage_path('framework/views')),
    'relative_hash' => env('VIEW_RELATIVE_HASH', true),
    'cache' => env('VIEW_CACHE', true),
];
