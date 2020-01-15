<?php

declare (strict_types=1);

namespace App\Exception\Http;

class Unauthorized extends Basic
{
    /** @var int */
    protected $httpErrorCode = 401;
}