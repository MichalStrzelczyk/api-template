<?php

declare (strict_types=1);

namespace App\Process;

use \Psr\Log\LoggerInterface;
use \Phalcon\Session\Manager;
use \Miinto\AuthService\Sdk;

class Factory
{
    /** @var ResponseFactoryInterface */
    protected $responseFactory;

    /** @var \Sdk\AuthService */
    protected $authService;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var \Phalcon\Session\Manager */
    protected $sessionManager;

    /**
     * Factory constructor.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param Sdk\AuthService $authService
     * @param LoggerInterface $logger
     * @param Manager $sessionManager
     */
    public function __construct(
        \Process\ResponseFactoryInterface $responseFactory,
        Sdk\AuthService $authService,
        LoggerInterface $logger,
        Manager $sessionManager
    ) {
        $this->responseFactory = $responseFactory;
        $this->authService = $authService;
        $this->logger = $logger;
        $this->sessionManager = $sessionManager;
    }

    /**
     * @return \App\Process\Auth\Login
     */
    public function createLoginProcess(): \App\Process\Auth\Login
    {
        $validator = new \App\Process\Auth\Validator\Login();
        $response = $this->responseFactory->createResponse();

        return new \App\Process\Auth\Login($response, $validator, $this->authService, $this->logger, $this->sessionManager);
    }

    /**
     * @return \App\Process\Auth\Logout
     */
    public function createLogoutProcess(): \App\Process\Auth\Logout
    {
        $validator = new \App\Process\Auth\Validator\Logout();
        $response = $this->responseFactory->createResponse();

        return new \App\Process\Auth\Logout($response, $validator, $this->logger, $this->sessionManager);
    }
}
