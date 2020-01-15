<?php

declare (strict_types=1);

namespace App\Exception\Http;

class BadRequest extends Basic
{
    /** @var int */
    protected $httpErrorCode = 400;
}