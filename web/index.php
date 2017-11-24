<?php

require_once dirname(__FILE__, 2) . '/vendor/autoload.php';

use Core\Application;

$app = new Application(true);
$app->run();
