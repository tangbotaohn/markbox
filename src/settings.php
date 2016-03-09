<?php

return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__.'/../public/themes/simple/',
        ],
    ],
    'webinfo' => [
        'title' => 'markbox title example',
        'description' => 'markbox is a markdown blog',
        'keyword' => 'markdown,blog,markbox',
    ],
    'users' => [
        ['username' => 'tmkook', 'password' => '123456', 'role' => 'master'],
    ],
];
