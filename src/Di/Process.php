<?php

declare (strict_types=1);

namespace App\Di;

/** @var \Phalcon\Di $di */

$di->setShared(
    'Process\Factory',
    function () use ($di) {
        return new \App\Process\Factory(
            $di->get('Process\ResponseFactory'),
            $di->get('Service\AuthClient'),
            $di->get('logger'),
            $di->get('session')
        );
    }
);

$di->setShared(
    'Process\ResponseFactory',
    function () use ($di) {
        return new \Process\ResponseFactory();
    }
);
