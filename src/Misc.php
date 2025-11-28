<?php

namespace Hexlet\Code\Misc;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

function redirectToUrl(
    ServerRequestInterface $request,
    string $routeName,
    array $routeParams = [],
    int $statusCode = 302
): ResponseInterface {
    $routeParser = RouteContext::fromRequest($request)->getRouteParser();
    $redirectUrl = $routeParser->urlFor($routeName, $routeParams);

    return (new Response())
        ->withHeader('Location', $redirectUrl)
        ->withStatus($statusCode);
}
