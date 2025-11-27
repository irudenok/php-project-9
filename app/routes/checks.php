<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Hexlet\Code\Connection;
use Hexlet\Code\Misc;
use Hexlet\Code\Query;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use Symfony\Component\DomCrawler\Crawler;

$app->post(
    '/urls/{url_id}/checks',
    function (
        ServerRequestInterface $request,
        Response $response,
        array $args
    ): ResponseInterface {
        $check['url_id'] = $args['url_id'];
        $check['date'] = date('Y-m-d H:i:s');

        $pdo = Connection::get()->connect();
        $checkedUrl = $pdo->query("SELECT name FROM urls WHERE id = {$args['url_id']}")->fetchColumn();

        try {
            $client = new Client();
            $guzzleResponse = $client->request('GET', $checkedUrl);
            $check['status_code'] = $guzzleResponse->getStatusCode();
            $htmlContent = $guzzleResponse->getBody()->getContents();
        } catch (TransferException $e) {
            $this->get('flash')->addMessage('failure', 'Произошла ошибка при проверке, не удалось подключиться');

            return Misc\redirectToUrl($request, 'show_url_info', ['id' => $args['url_id']]);
        }

        $crawler = new Crawler($htmlContent);

        $h1Element = $crawler->filter('h1')->first();
        if ($h1Element->count() > 0) {
            $check['h1'] = $h1Element->text();
        }

        $titleElement = $crawler->filter('title')->first();
        if ($titleElement->count() > 0) {
            $check['title'] = $titleElement->text();
        }

        $descElement = $crawler->filter('meta[name="description"]')->first();
        if ($descElement->count() > 0) {
            $check['description'] = $descElement->attr('content');
        }

        if (isset($check['status_code'])) {
            try {
                $query = new Query($pdo, 'url_checks');
                $newId = $query->insertValuesChecks($check);
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }

            $this->get('flash')->addMessage('success', 'Страница успешно проверена');
        }

        return Misc\redirectToUrl($request, 'show_url_info', ['id' => $args['url_id']]);
    }
);
