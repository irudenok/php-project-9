<?php

use Hexlet\Code\Connection;
use Hexlet\Code\Query;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use Hexlet\Code\Misc;

$app->get('/urls', function (ServerRequestInterface $request, Response $response): Response {
    $pdo = Connection::get()->connect();
    $allUrls = $pdo->query("SELECT * FROM urls")->fetchAll(\PDO::FETCH_ASSOC);
    $recentChecks = $pdo->query("SELECT DISTINCT ON (url_id) url_id, created_at, status_code
                                 FROM url_checks
                                 ORDER BY url_id, created_at DESC;")->fetchAll(\PDO::FETCH_ASSOC);

    $combined = array_map(function ($url) use ($recentChecks) {
        foreach ($recentChecks as $recCheck) {
            if ($url['id'] === $recCheck['url_id']) {
                $url['last_check_time'] = $recCheck['created_at'];
                $url['status_code'] = $recCheck['status_code'];
            }
        }
        return $url;
    }, $allUrls);

    $params = ['urls' => array_reverse($combined)];
    return $this->get('renderer')->render($response, 'list.phtml', $params);
})->setName('list');

$app->get('/urls/{id}', function (ServerRequestInterface $request, Response $response, array $args): Response {

    $pdo = Connection::get()->connect();
    $allUrls = $pdo->query("SELECT * FROM urls")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($allUrls as $item) {
        if ($item['id'] == $args['id']) {
            $urlFound = $item;
        }
    }

    if (!isset($urlFound)) {
        return $response->withStatus(404);
    }

    $checks = $pdo->query("SELECT * FROM url_checks WHERE url_id = {$args['id']}")->fetchAll(\PDO::FETCH_ASSOC);
    $flashes = $this->get('flash')->getMessages();
    $params = ['url' => $urlFound, 'checks' => array_reverse($checks), 'flash' => $flashes];
    return $this->get('renderer')->render($response, 'view.phtml', $params);
})->setName('show_url_info');

$app->post('/urls', function (ServerRequestInterface $request, Response $response): mixed {
    $url = $request->getParsedBody()['url'];

    $url['date'] = date('Y-m-d H:i:s');
    $errors = [];

    $scheme = parse_url($url['name'], PHP_URL_SCHEME);
    if (!filter_var($url['name'], FILTER_VALIDATE_URL) || !in_array($scheme, ['http', 'https'])) {
        $errors['name'] = 'Некорректный URL';
    }

    if (strlen($url['name']) < 1) {
        $errors['name'] = 'URL не должен быть пустым';
    }

    if (count($errors) === 0) {
        $url['name'] = parse_url($url['name'], PHP_URL_SCHEME) . "://" . parse_url($url['name'], PHP_URL_HOST);
        $pdo = Connection::get()->connect();
        $currentUrls = $pdo->query("SELECT * FROM urls")->fetchAll(PDO::FETCH_ASSOC);

        foreach ($currentUrls as $item) {
            if ($item['name'] === $url['name']) {
                $urlFound = $item;
                $idFound = $item['id'];
            }
        }

        $newId = null;
        if (!isset($urlFound)) {
            try {
                $pdo = Connection::get()->connect();
                $query = new Query($pdo, 'urls');
                $newId = $query->insertValues($url['name'], $url['date']);
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
            $this->get('flash')->addMessage('success', 'Страница успешно добавлена');
        } else {
            $this->get('flash')->addMessage('success', 'Страница уже существует');
        }

        return Misc\redirectToUrl($request, 'show_url_info', ['id' => $idFound ?? $newId]);
    }

    $params = ['url' => $url, 'errors' => $errors];

    return $this->get('renderer')->render($response->withStatus(422), "main.phtml", $params);
});
