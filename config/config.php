<?php
$config = [
    'eventstore' => [
        'host' => '127.0.0.1',
        'port' => 2113,
        'user' => 'admin',
        'pass' => 'changeit'
    ],
    'eventstore-async' => [
        'host' => '127.0.0.1',
        'port' => 1113,
        'user' => 'admin',
        'pass' => 'changeit'
    ],
    'pdo-mariadb' => [
        'dsn' => 'mysql:host=localhost;dbname=accounting',
        'user' => 'root',
        'pass' => 'id10t'
    ]
];
