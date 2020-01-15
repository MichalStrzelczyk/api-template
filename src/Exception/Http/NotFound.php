<?php

declare (strict_types=1);

namespace App\Exception\Http;

class NotFound extends Basic
{
    /** @var int */
    protected $httpErrorCode = 404;
}