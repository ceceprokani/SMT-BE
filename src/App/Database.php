<?php

declare(strict_types=1);

use Pimple\Container;

/** @var Container $container */
$container['db'] = static function () : Pecee\Pixie\Connection {
    $config = [
        'driver'    => 'mysql', // Db driver
        'host'      => $_ENV['DB_HOST'] ?: $_SERVER['DB_HOST'],
        'port'      => $_ENV['DB_PORT'] ?: $_SERVER['DB_PORT'],
        'database'  => $_ENV['DB_NAME'] ?: $_SERVER['DB_NAME'],
        'username'  => $_ENV['DB_USER'] ?: $_SERVER['DB_USER'],
        'password'  => $_ENV['DB_PASS'] ?: $_SERVER['DB_PASS'],
        'charset'   => 'utf8', // Optional
        'collation' => 'utf8_unicode_ci', // Optional
        'options'   => [ // PDO constructor options, optional
            PDO::ATTR_TIMEOUT => 5,
            PDO::ATTR_EMULATE_PREPARES => false,
        ],
    ];
    $connection = new \Pecee\Pixie\Connection('mysql', $config);
    return $connection;
};