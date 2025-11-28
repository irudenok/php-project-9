<?php

use Hexlet\Code\Connection;
use Hexlet\Code\UrlRepository;
use Hexlet\Code\UrlCheckRepository;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use Illuminate\Support\Collection;
use Valitron\Validator;

use function Hexlet\Code\Misc\redirectToUrl;

return function ($app): void {
    $app->get('/urls', function (ServerRequestInterface $request, Response $response): Response {
        $pdo = Connection::get()->connect();
        $urlRepository = new UrlRepository($pdo);
        $urlCheckRepository = new UrlCheckRepository($pdo);

        $allUrls = $urlRepository->findAll();
        $recentChecks = $urlCheckRepository->findLatestChecks();

        $checksCollection = Collection::make($recentChecks)->keyBy('url_id');

        $combined = array_map(function ($url) use ($checksCollection) {
            $check = $checksCollection->get($url['id']);

            $url['last_check_time'] = $check['created_at'] ?? null;
            $url['status_code'] = $check['status_code'] ?? null;

            return $url;
        }, $allUrls);

        $params = [
            'urls' => $combined,
            'currentPage' => '/urls'
        ];
        $output = $this->get('renderer')->render('urls/index.phtml', $params);
        $response->getBody()->write($output);
        return $response;
    })->setName('list');

    $app->get(
        '/urls/{id:[0-9]+}',
        function (
            ServerRequestInterface $request,
            Response $response,
            array $args
        ): Response {
            $pdo = Connection::get()->connect();
            $urlRepository = new UrlRepository($pdo);
            $urlCheckRepository = new UrlCheckRepository($pdo);

            $url = $urlRepository->findById((int) $args['id']);

            if (!$url) {
                return $response->withStatus(404);
            }

            $checks = $urlCheckRepository->findByUrlId((int) $args['id']);
            $flashes = $this->get('flash')->getMessages();
            $params = [
                'url' => $url,
                'checks' => $checks,
                'flash' => $flashes,
                'currentPage' => '/urls'
            ];
            $output = $this->get('renderer')->render('urls/show.phtml', $params);
            $response->getBody()->write($output);
            return $response;
        }
    )->setName('show_url_info');

    $app->post('/urls', function (ServerRequestInterface $request, Response $response): mixed {
        $body = $request->getParsedBody();

        /** @var array<string, mixed> $body */
        $urlData = $body['url'] ?? [];
        $urlName = isset($urlData['name']) ? (string) $urlData['name'] : '';

        // Создаем валидатор с правильной структурой данных
        $data = ['name' => $urlName];
        $validator = new Validator($data);

        $validator->rule('required', 'name')->message('URL не должен быть пустым');
        $validator->rule('lengthMin', 'name', 1)->message('URL не должен быть пустым');
        $validator->rule('url', 'name')->message('Некорректный URL');
        $validator->rule('urlActive', 'name')->message('Некорректный URL');

        Validator::addRule('urlActive', function ($field, $value, $params, $fields) {
            if (empty($value)) {
                return false;
            }
            $scheme = parse_url($value, PHP_URL_SCHEME);
            return in_array($scheme, ['http', 'https']);
        }, 'должен иметь схему http или https');

        if (!$validator->validate()) {
            $errors = $validator->errors();
            // Берем первую ошибку для каждого поля
            $firstErrors = [];
            if (is_array($errors)) {
                foreach ($errors as $field => $fieldErrors) {
                    if (is_array($fieldErrors)) {
                        $firstErrors[$field] = $fieldErrors[0] ?? $fieldErrors;
                    } else {
                        $firstErrors[$field] = $fieldErrors;
                    }
                }
            }

            $params = [
                'url' => ['name' => $urlName],
                'errors' => $firstErrors,
                'currentPage' => '/'
            ];
            $output = $this->get('renderer')->render('home.phtml', $params);
            $response->getBody()->write($output);
            return $response->withStatus(422);
        }

        $normalizedUrl = parse_url($urlName, PHP_URL_SCHEME) . "://" . parse_url($urlName, PHP_URL_HOST);

        $pdo = Connection::get()->connect();
        $urlRepository = new UrlRepository($pdo);

        $existingUrl = $urlRepository->findByName($normalizedUrl);

        $id = null;
        if (!$existingUrl) {
            try {
                $id = $urlRepository->save($normalizedUrl, date('Y-m-d H:i:s'));
                $this->get('flash')->addMessage('success', 'Страница успешно добавлена');
            } catch (\PDOException $e) {
                $this->get('flash')->addMessage('failure', 'Ошибка при сохранении URL');
                return redirectToUrl($request, 'home');
            }
        } else {
            $id = $existingUrl['id'];
            $this->get('flash')->addMessage('success', 'Страница уже существует');
        }

        return redirectToUrl($request, 'show_url_info', ['id' => $id]);
    });
};
