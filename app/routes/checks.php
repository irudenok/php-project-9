<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Hexlet\Code\Connection;
use Hexlet\Code\UrlRepository;
use Hexlet\Code\UrlCheckRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use Symfony\Component\DomCrawler\Crawler;

use function Hexlet\Code\Misc\redirectToUrl;

return function ($app) {
    $app->post(
        '/urls/{url_id:[0-9]+}/checks',
        function (
            ServerRequestInterface $request,
            Response $response,
            array $args
        ): ResponseInterface {
            $pdo = Connection::get()->connect();
            $urlRepository = new UrlRepository($pdo);
            $urlCheckRepository = new UrlCheckRepository($pdo);

            $url = $urlRepository->findById((int) $args['url_id']);

            if (!$url) {
                $this->get('flash')->addMessage('failure', 'URL не найден в базе данных');
                return redirectToUrl($request, 'show_url_info', ['id' => $args['url_id']]);
            }

            $check = [
                'url_id' => $args['url_id'],
                'date' => date('Y-m-d H:i:s')
            ];

            try {
                $client = new Client();
                $guzzleResponse = $client->request('GET', $url['name']);

                $check['status_code'] = $guzzleResponse->getStatusCode();
                $htmlContent = $guzzleResponse->getBody()->getContents();
            } catch (TransferException $e) {
                $this->get('flash')->addMessage('failure', 'Произошла ошибка при проверке, не удалось подключиться');
                return redirectToUrl($request, 'show_url_info', ['id' => $args['url_id']]);
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

            if (!empty($check['status_code'])) {
                try {
                    $urlCheckRepository->save($check);
                    $this->get('flash')->addMessage('success', 'Страница успешно проверена');
                } catch (\PDOException $e) {
                    $this->get('flash')->addMessage('failure', 'Ошибка при сохранении проверки');
                }
            }

            return redirectToUrl($request, 'show_url_info', ['id' => $args['url_id']]);
        }
    );
};
