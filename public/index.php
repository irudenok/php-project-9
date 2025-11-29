<?php

if (($_ENV['APP_ENV'] ?? 'production') === 'production') {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
}

session_start();

require __DIR__ . '/../app/bootstrap.php';

$app = createApp();
$app->run();
