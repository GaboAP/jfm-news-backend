<?php

return [
    // memory | file
    'repository' => env('ARTICLE_REPOSITORY', 'memory'),

    // Only used by FileArticleRepository
    'file' => [
        'disk' => env('ARTICLE_FILE_DISK', 'local'),
        'path' => env('ARTICLE_FILE_PATH', 'articles_store.json')
    ],
];
