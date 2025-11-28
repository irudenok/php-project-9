<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

return function ($app): void {
    $app->get('/', function (ServerRequestInterface $request, Response $response): ResponseInterface {
        $params = [
            'currentPage' => '/',
            'url' => $url ?? [],
            'errors' => $errors ?? []
        ];

        $output = $this->get('renderer')->render('home.phtml', $params);
        $response->getBody()->write($output);

        return $response;
    })->setName('home');
};
