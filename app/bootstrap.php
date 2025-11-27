<?php

error_reporting(0);
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');

$autoloadPath1 = __DIR__ . '/../../../autoload.php';
$autoloadPath2 = __DIR__ . '/../vendor/autoload.php';

if (file_exists($autoloadPath1)) {
    require_once $autoloadPath1;
} else {
    require_once $autoloadPath2;
}

use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use Slim\Flash\Messages;
use DI\Container;
use Hexlet\Code\Connection;
use Hexlet\Code\Misc;

session_start();

try {
    $pdo = Connection::get()->connect();

    if (!isset($_SESSION['start'])) {
        if (Misc\tableExists($pdo, "url_checks")) {
            $pdo->exec("TRUNCATE url_checks");
        }
        if (Misc\tableExists($pdo, "urls")) {
            $pdo->exec("TRUNCATE urls CASCADE");
        }
        $_SESSION['start'] = true;
    }

    if (!Misc\tableExists($pdo, "urls")) {
        $pdo->exec("
            CREATE TABLE urls (
                id bigint PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
                name varchar(255),
                created_at timestamp
            )
        ");
    }

    if (!Misc\tableExists($pdo, "url_checks")) {
        $pdo->exec("
            CREATE TABLE url_checks (
                id bigint PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
                url_id bigint REFERENCES urls (id),
                status_code smallint,
                h1 varchar(255),
                title varchar(255),
                description text,
                created_at timestamp
            )
        ");
    }
} catch (\PDOException $e) {
    echo $e->getMessage();
}

$container = new Container();

$container->set('renderer', function () {
    return new PhpRenderer(__DIR__ . '/../templates');
});

$container->set('flash', function () {
    return new Messages();
});

$app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);

return $app;
