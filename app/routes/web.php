<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

return function ($app): void {
    $app->get('/', function (ServerRequestInterface $request, Response $response): ResponseInterface {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $params = [
            'url' => [],
            'errors' => [],
            'routeParser' => $routeParser
        ];

        $renderer = $this->get('renderer');
        $output = $renderer->render('home.phtml', $params);
        $response->getBody()->write($output);

        return $response;
    })->setName('home');
};
