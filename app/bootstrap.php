<?php // phpcs:ignore warning

require_once __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Flash\Messages;
use Hexlet\Code\Renderer;
use Slim\App;

function createApp(): App
{
    $app = AppFactory::create();
    $app->addErrorMiddleware(true, true, true);

    $renderer = new Renderer(__DIR__ . '/../templates');
    $flash = new Messages();

    $container = $app->getContainer();

    $container->set('renderer', function () use ($renderer): Renderer {
        return $renderer;
    });

    $container->set('flash', function () use ($flash): Messages {
        return $flash;
    });

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
