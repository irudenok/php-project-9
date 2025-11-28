<?php // phpcs:ignore warning

require_once __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Flash\Messages;
use Hexlet\Code\Renderer;
use DI\Container;

function createApp(): \Slim\App
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
