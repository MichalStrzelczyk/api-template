<?php
declare (strict_types=1);

namespace App;

// initialize time profiling
$start = microtime(true);

// define path constants
define('APPLICATION_ENVIRONMENT', $_SERVER['APPLICATION_ENVIRONMENT']);
define('BASE_PATH', realpath('..'));
define('CONFIG_PATH', realpath('../config'));
define('VENDOR_PATH', realpath('../vendor'));
define('SRC_PATH', realpath('../src'));

// add vendor based autoloading
require_once VENDOR_PATH . '/autoload.php';

$di = new Di();
Bootstrap::initializeServices($di);
$app = new \Phalcon\Mvc\Application($di);
$app->useImplicitView(false);
$app->handle($_SERVER['REQUEST_URI'])->send();
