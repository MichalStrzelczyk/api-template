<?php

declare (strict_types=1);

namespace App\Exception\Http;

use Throwable;

class Basic extends \RuntimeException
{
    /** @var array */
    protected $errorContainer = [];

    /** @var int */
    protected $httpErrorCode = 500;

    /**
     * Basic constructor.
     * @param array $errorContainer
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(array $errorContainer = [], $message = "", $code = 0, Throwable $previous = null)
    {
        $this->errorContainer = $errorContainer;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getErrorContainer(): array
    {
        return $this->errorContainer;
    }

    /**
     * @return int
     */
    public function getHttpErrorCode(): int
    {
        return $this->httpErrorCode;
    }
}