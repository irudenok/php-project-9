<?php // phpcs:ignore warning

require_once __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Flash\Messages;
use Hexlet\Code\Renderer;
use Hexlet\Code\UrlRepository;
use Hexlet\Code\UrlCheckRepository;
use Hexlet\Code\Connection;
use Slim\App;
use DI\Container;

/**
 * @return App<\Psr\Container\ContainerInterface>
 */
function createApp(): App
{
    $templatesPath = __DIR__ . '/../templates';
    if (!file_exists($templatesPath) || !is_dir($templatesPath)) {
        throw new RuntimeException("Templates directory not found: {$templatesPath}");
    }

    $container = new Container();

    $container->set('renderer', function () use ($templatesPath) {
        return new Renderer($templatesPath);
    });

    $container->set('flash', function () {
        return new Messages();
    });

    $container->set('pdo', function () {
        return Connection::get()->connect();
    });

    $container->set('urlRepository', function ($container) {
        return new UrlRepository($container->get('pdo'));
    });

    $container->set('urlCheckRepository', function ($container) {
        return new UrlCheckRepository($container->get('pdo'));
    });

    AppFactory::setContainer($container);
    $app = AppFactory::create();

    $errorMiddleware = $app->addErrorMiddleware(
        $_ENV['APP_DEBUG'] ?? false, // displayErrorDetails - только в режиме отладки
        true, // logErrors
        true  // logErrorDetails
    );

    $routes = [
        'web.php',
        'urls.php',
        'checks.php'
    ];

    foreach ($routes as $routeFile) {
        $routePath = __DIR__ . '/routes/' . $routeFile;
        if (!file_exists($routePath)) {
            throw new RuntimeException("Route file not found: {$routePath}");
        }

        $route = require $routePath;

        $route($app);
    }

    return $app;
}
