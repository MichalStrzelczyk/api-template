<?php

declare (strict_types=1);

namespace App\Di;

/** @var \Phalcon\Di $di */

$di->setShared(
    'PayloadValidator\ValidatorFactory',
    function () use ($di) {
        return new \PayloadValidator\Validator\Factory();
    }
);

$di->setShared(
    'PayloadValidator\SchemaFactory',
    function () use ($di) {
        return new \PayloadValidator\Schema\Factory();
    }
);
