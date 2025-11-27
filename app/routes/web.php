<?php

global $app;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

$app->get('/', function (ServerRequestInterface $request, Response $response): ResponseInterface {
    return $this->get('renderer')->render($response, 'main.phtml');
})->setName('home');
