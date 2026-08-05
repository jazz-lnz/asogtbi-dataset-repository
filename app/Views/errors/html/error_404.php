<?php

helper('url');

$error = [
    'code' => '404',
    'title' => 'Page Not Found',
    'message' => "The page you're looking for may have been moved, deleted, or the URL is incorrect.",
    'actions' => [
        [
            'label' => 'Go to Home',
            'href' => site_url('/'),
            'variant' => 'primary',
        ],
        [
            'label' => 'Go Back',
            'behavior' => 'history-back',
            'variant' => 'secondary',
        ],
    ],
];

include __DIR__ . DIRECTORY_SEPARATOR . '_error_layout.php';
