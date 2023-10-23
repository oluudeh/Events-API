<?php

$container = require_once __DIR__ . '/src/bootstrap.php';


use Monolog\Level;
use Monolog\Logger;
use App\EventApp;
use Monolog\Handler\StreamHandler;
use Symfony\Component\HttpFoundation\Request;


$request = Request::createFromGlobals();
$routes = require_once __DIR__ . '/src/routes.php';

// create log channel
$requestLog = new Logger('request');
$requestLog->pushHandler(new StreamHandler(__DIR__.  '/storage/logs/requests.log', Level::Debug));

$app = new EventApp($routes, $requestLog, $container);
$response = $app->handle($request);

$response->send();
