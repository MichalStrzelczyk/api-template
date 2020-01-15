<?php

declare (strict_types=1);

namespace App\Process\Auth;

class Logout extends \Process\AbstractProcess
{
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var \Phalcon\Session\Manager */
    protected $sessionManager;

    /**
     * Logout constructor.
     *
     * @param \Process\ResponseInterface $processResponse
     * @param \Process\ValidatorInterface $validator
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Phalcon\Session\Manager $sessionManager
     */
    public function __construct(
        \Process\ResponseInterface $processResponse,
        \Process\ValidatorInterface $validator,
        \Psr\Log\LoggerInterface $logger,
        \Phalcon\Session\Manager $sessionManager
    ) {
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
        try {
            $this->sessionManager->destroy();
        } catch (\Throwable $t) {
            $this->logger->critical($t->getMessage());

            $this->getResponse()->addError('1004', 'logoutError');
        }
    }
}
