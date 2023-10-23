<?php

return [
    '/' => [
        'GET' => ['App\Controllers\EventsController', 'index']
    ],
    '/events' => [
        'GET' => ['App\Controllers\EventsController', 'fetchEvents']
    ]
];
