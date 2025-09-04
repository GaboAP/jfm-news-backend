<?php

return [
    // memory | file
    'repository' => env('MEDIA_REPOSITORY', 'memory'),

    // Only used by FileMediaRepository
    'file' => [
        'disk' => env('MEDIA_FILE_DISK', 'local'),
        'path' => env('MEDIA_FILE_PATH', 'media_store.json')
    ],
];
