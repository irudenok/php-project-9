<?php

$app = require __DIR__ . '/../app/bootstrap.php';

require __DIR__ . '/../app/routes/web.php';
require __DIR__ . '/../app/routes/urls.php';
require __DIR__ . '/../app/routes/checks.php';

$app->run();
