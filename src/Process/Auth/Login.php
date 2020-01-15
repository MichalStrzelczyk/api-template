<?php

declare (strict_types=1);

namespace App\Process\Auth;

use \Miinto\AuthService\Sdk;

class Login extends \Process\AbstractProcess
{
    /** @var Sdk\AuthService */
    protected $authService;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var \Phalcon\Session\Manager */
    protected $sessionManager;

    /**
     * Login constructor.
     *
     * @param \Process\ResponseInterface $processResponse
     * @param \Process\ValidatorInterface $validator
     * @param Sdk\AuthService $authService
     */
    public function __construct(
        \Process\ResponseInterface $processResponse,
        \Process\ValidatorInterface $validator,
        Sdk\AuthService $authService,
        \Psr\Log\LoggerInterface $logger,
        \Phalcon\Session\Manager $sessionManager
    ) {
        $this->authService = $authService;
        $this->logger = $logger;
        $this->sessionManager = $sessionManager;

        parent::__construct($processResponse, $validator);
    }

    /**
     * @param array $options
     *
     * @return mixed|void
     */
    protected function execute(array $options = []): void
    {
        $login = $options['login'];
        $password = $options['password'];

        try {
            $data = $this->authService->createChannel($login, $password);
            $this->sessionManager->set('user', $data);
            $this->getResponse()->setData(['channel' => $data]);
        } catch (\Throwable $t) {
            $this->logger->critical($t->getMessage());

            $this->getResponse()->addError('1003', 'incorrectCredentials');
        }
    }
}
