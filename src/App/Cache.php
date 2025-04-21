<?php

declare(strict_types=1);

use Pimple\Container;

/** @var Container $container */
$container['cache_redis'] = static function () : Predis\Client {
    
    $REDIS_SERVER_HOST = isset($_SERVER['REDIS_SERVER_HOST']) ? $_SERVER['REDIS_SERVER_HOST'] : '127.0.0.1';
    $REDIS_SERVER_PORT = isset($_SERVER['REDIS_SERVER_PORT']) ? $_SERVER['REDIS_SERVER_PORT'] : '6379';
    $REDIS_SERVER_PASSWORD = isset($_SERVER['REDIS_SERVER_PASSWORD']) ? $_SERVER['REDIS_SERVER_PASSWORD'] : '';
    $REDIS_SERVER_DATABASE = isset($_SERVER['REDIS_SERVER_DATABASE']) ? $_SERVER['REDIS_SERVER_DATABASE'] : '0';

    $config = [
        'schema' => 'tcp',
        'host' => $REDIS_SERVER_HOST,
        'port' => $REDIS_SERVER_PORT,
        'password' => $REDIS_SERVER_PASSWORD,
        'database' => $REDIS_SERVER_DATABASE
    ];

    $connection = new \Predis\Client($config);
    return $connection;
};