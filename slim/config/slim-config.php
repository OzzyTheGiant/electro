<?php
return [
    'settings' => [
        'displayErrorDetails' => true,
        'logger' => [
            'name' => 'slim-app',
            'level' => Monolog\Logger::DEBUG,
            'path' => __DIR__ . '/../logs/slim-application.log',
		],
		'atlas' => require_once("atlas-skeleton-config.php")
    ],
];