<?php

use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use Illuminate\Support\Collection;
use Valitron\Validator;
use Slim\Routing\RouteContext;

use function Hexlet\Code\Misc\redirectToUrl;

return function ($app): void {
    $app->get('/urls', function (ServerRequestInterface $request, Response $response): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $urlRepository = $this->get('urlRepository');
        $urlCheckRepository = $this->get('urlCheckRepository');

        $allUrls = $urlRepository->findAll();
        $recentChecks = $urlCheckRepository->findLatestChecks();

        /** @var array<int, array<string, mixed>> $recentChecks */
        $checksCollection = Collection::make($recentChecks)->keyBy('url_id');

        $params = [
            'urls' => $allUrls,
            'checksCollection' => $checksCollection,
            'routeParser' => $routeParser
        ];

        $output = $this->get('renderer')->render('urls/index.phtml', $params);
        $response->getBody()->write($output);
        return $response;
    })->setName('urls.index');

    $app->get(
        '/urls/{id:[0-9]+}',
        function (
            ServerRequestInterface $request,
            Response $response,
            array $args
        ): Response {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();

            $urlRepository = $this->get('urlRepository');
            $urlCheckRepository = $this->get('urlCheckRepository');

            $url = $urlRepository->findById((int) $args['id']);

            if (!$url) {
                $params = [
                    'routeParser' => $routeParser
                ];
                $output = $this->get('renderer')->render('404.phtml', $params);
                $response->getBody()->write($output);
                return $response->withStatus(404);
            }

            $checks = $urlCheckRepository->findByUrlId((int) $args['id']);
            $flashes = $this->get('flash')->getMessages();
            $params = [
                'url' => $url,
                'checks' => $checks,
                'flash' => $flashes,
                'routeParser' => $routeParser
            ];

            $output = $this->get('renderer')->render('urls/show.phtml', $params);
            $response->getBody()->write($output);
            return $response;
        }
    )->setName('urls.show');

    $app->post('/urls', function (ServerRequestInterface $request, Response $response): mixed {
        $body = $request->getParsedBody();
        /** @var array<string, mixed> $body */
        $urlData = $body['url'] ?? [];

        $urlName = $urlData['name'] ?? '';

        $data = ['name' => $urlName];
        $validator = new Validator($data);

        $validator->rule('required', 'name')->message('URL не должен быть пустым');
        $validator->rule('lengthMin', 'name', 1)->message('URL не должен быть пустым');
        $validator->rule('url', 'name')->message('Некорректный URL');

        if (!$validator->validate()) {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $params = [
                'url' => ['name' => $urlName],
                'errors' => $validator->errors(),
                'routeParser' => $routeParser
            ];

            $output = $this->get('renderer')->render('home.phtml', $params);
            $response->getBody()->write($output);
            return $response->withStatus(422);
        }

        $normalizedUrl = parse_url($urlName, PHP_URL_SCHEME) . "://" . parse_url($urlName, PHP_URL_HOST);

        $urlRepository = $this->get('urlRepository');
        $existingUrl = $urlRepository->findByName($normalizedUrl);

        $id = null;
        $flash = $this->get('flash');

        if (!$existingUrl) {
            try {
                $id = $urlRepository->save($normalizedUrl, date('Y-m-d H:i:s'));
                $flash->addMessage('success', 'Страница успешно добавлена');
            } catch (\PDOException $e) {
                $flash->addMessage('failure', 'Ошибка при сохранении URL');
                return redirectToUrl($request, 'home');
            }
        } else {
            $id = $existingUrl['id'];
            $flash->addMessage('success', 'Страница уже существует');
        }

        return redirectToUrl($request, 'urls.show', ['id' => $id]);
    });
};
