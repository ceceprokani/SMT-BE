<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface;

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$customErrorHandler = function (
    ServerRequestInterface $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails
) use ($app): Response {
    $statusCode = 500;
    if (is_int($exception->getCode()) &&
        $exception->getCode() >= 400 &&
        $exception->getCode() <= 599
    ) {
        $statusCode = $exception->getCode();
    }
    $className = new \ReflectionClass(get_class($exception));
    $data = [
        'message' => $exception->getMessage(),
        'class' => $className->getShortName(),
        'status' => 'error',
        'code' => $statusCode,
        'file' => $exception->getFile() . ":" . $exception->getLine()
    ];
    $body = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    $response = $app->getResponseFactory()->createResponse();
    $response->getBody()->write($body);
    
    $serverInfo = $request->getServerParams();

    $log_path = __DIR__ . '/../../storage/log/';

    if(!is_dir($log_path)) {
        mkdir($log_path, 0755, true);
    }

    // create a log channel
    $log = new Logger('oeltima_attendancesystem');
    $log->pushHandler(new StreamHandler($log_path . 'error.log', Level::Error));

    $log->error($exception->getMessage(), array($statusCode, $className->getShortName(), $serverInfo['REQUEST_URI']));

    return $response
        ->withStatus($statusCode)
        ->withHeader('Content-type', 'application/problem+json');
};

return $customErrorHandler;
