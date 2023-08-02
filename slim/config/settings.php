<?php
return [
    'displayErrorDetails' => true,
    'logger' => [
        'name' => 'slim-app',
        'level' => Monolog\Logger::DEBUG,
        'path' => $_ENV["LOG_FILE_PATH"],
    ],
    'db' => [
        'driver' => $_ENV["DB_CONNECTION"],
        'host' => $_ENV["DB_HOST"],
        'database' => $_ENV["DB_DATABASE"],
        'username' => $_ENV["DB_USER"],
        'password' => $_ENV["DB_PASSWORD"],
        'charset' => 'utf8',
    ]
];
