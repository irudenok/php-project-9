<?php

error_reporting(0);
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');

require_once __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Flash\Messages;
use DI\Container;
use Hexlet\Code\Renderer;

session_start();

function createApp()
{
    $container = new Container();

    $container->set('renderer', function () {
        return new Renderer(__DIR__ . '/../templates');
    });

    $container->set('flash', function () {
        return new Messages();
    });

    $app = AppFactory::createFromContainer($container);
    $app->addErrorMiddleware(true, true, true);

    $routes = [
        'web.php',
        'urls.php',
        'checks.php'
    ];

    foreach ($routes as $routeFile) {
        $route = require __DIR__ . '/routes/' . $routeFile;
        $route($app);
    }

    return $app;
}
