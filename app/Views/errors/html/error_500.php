<?php

helper('url');

$error = [
    'code' => '500',
    'title' => 'Internal Server Error',
    'message' => 'Sorry! Something went wrong on our end. Please try again in a moment or go back and retry the action.',
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
    'illustrationClass' => 'error-visual--plain',
    'illustration' => '<img class="error-visual__image" src="' . esc(base_url('assets/img/error_500.png'), 'attr') . '" alt="" loading="eager" decoding="async">',
];

include __DIR__ . DIRECTORY_SEPARATOR . '_error_layout.php';
