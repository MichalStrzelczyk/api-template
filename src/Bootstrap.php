<?php

declare (strict_types=1);

namespace App;

class Bootstrap
{
    /**
     * Initialize all basic services
     *
     * @param \Phalcon\Di\DiInterface $di
     */
    static public function initializeServices(\Phalcon\Di\DiInterface $di): void
    {
        static::initializeConfig($di);
        static::initializeErrors($di);
        static::initializeLogger($di);
        static::initializeErrorHandler($di);
        static::initializeErrorHandler($di);
        static::initializeRouter($di);
        static::initializeUrl($di);
        static::initializeRouter($di);
        static::initializeRequest($di);
        static::initializeResponse($di);
        static::initializeEventManager($di);
        static::initializeDispatcher($di);
    }

    /**
     * Initialize
     *
     * @param \Phalcon\Di\DiInterface $di
     */
    protected static function initializeErrors(\Phalcon\Di\DiInterface $di): void
    {
        $debugMode = $di->get('config')->basic->debug_mode;
        $displayErrors = 'On';
        if ($debugMode === null || $debugMode == false) {
            $displayErrors = 'Off';
        }

        error_reporting(E_ALL);
        ini_set('display_errors', $displayErrors);
        ini_set('display_startup_errors', $displayErrors);
    }

    /**
     * @param \Phalcon\Di\DiInterface $di
     */
    protected static function initializeErrorHandler(\Phalcon\Di\DiInterface $di): void
    {
        $logger = $di->get('logger');
        $environment = APPLICATION_ENVIRONMENT;
        \preg_match('/^production|development|sandbox|staging|uat|local/', $environment, $matches);
        $environment = \reset($matches);

        \set_exception_handler(
            function (\Throwable $t) use ($logger, $environment) {
                // Log error
                $logger->critical($t->getMessage());
                $logger->critical(
                    \json_encode(
                        [
                            'message' => $t->getMessage(),
                            'code' => $t->getCode(),
                            'line' => $t->getLine(),
                            'file' => $t->getFile(),
                            'trace' => $t->getTrace()
                        ]
                    )
                );

                $response = new \Phalcon\Http\Response();
                $response->setStatusCode(500);
                $response->sendHeaders();

                switch ($environment) {
                    case 'uat':
                    case 'production':
                        $response->redirect('/500')->send();
                        break;
                    default:
                        throw $t;
                }
            }
        );
    }

    /**
     * Initialize router
     *
     * @param \Phalcon\Di\DiInterface $di
     */
    protected static function initializeRouter(\Phalcon\Di\DiInterface $di): void
    {
        $di->setShared(
            'router',
            function () use ($di) {
                $router = new \Phalcon\Mvc\Router(false);
                $router->setDefaultNamespace('Controller');
                $router->notFound(
                    [
                        'namespace' => 'App\\Controller',
                        'controller' => 'error',
                        'action' => 'notFound',
                    ]
                );

                $pathToRoutes = CONFIG_PATH . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR . '*.json';
                $acl = [];
                $validation = [];

                foreach (\glob($pathToRoutes) as $filename) {
                    if (!\is_readable($filename)) {
                        throw new \RuntimeException(\sprintf('Route file %s is not readable.', $filename));
                    }

                    $routes = \json_decode(\file_get_contents($filename), true);
                    if ($routes === null) {
                        throw new \RuntimeException('Json file with routes is not able to decode.');
                    }

                    foreach ($routes as $routeConfiguration) {
                        $router->add(
                            $routeConfiguration['route'],
                            $routeConfiguration['bind'],
                            [$routeConfiguration['method']]
                        );

                        // Basic mapping
                        if (\is_string($routeConfiguration['bind'])) {
                            $aclKey = \strtolower(
                                    $routeConfiguration['method']
                                ) . "::controller::" . $routeConfiguration['bind'];
                        } else {
                            $aclKey = \strtolower(
                                \implode(
                                    '::',
                                    [
                                        $routeConfiguration['method'],
                                        $routeConfiguration['bind']['namespace'],
                                        $routeConfiguration['bind']['controller'],
                                        $routeConfiguration['bind']['action']
                                    ]
                                )
                            );
                        }

                        isset($routeConfiguration['acl']) and $acl[$aclKey] = $routeConfiguration['acl'];
                        isset($routeConfiguration['validation']) and $validation[$aclKey] = $routeConfiguration['validation'];
                    }
                }

                $di->setShared(
                    'acl',
                    function () use ($acl) {
                        return $acl;
                    }
                );

                $di->setShared(
                    'validation',
                    function () use ($validation) {
                        return $validation;
                    }
                );

                return $router;
            }
        );
    }

    /**
     * Initialize config
     *
     * @param \Phalcon\Di\DiInterface $di
     */
    protected static function initializeConfig(\Phalcon\Di\DiInterface $di): void
    {
        $di->setShared(
            'config',
            function () {
                $pathToConfigFile = CONFIG_PATH . DIRECTORY_SEPARATOR . APPLICATION_ENVIRONMENT . DIRECTORY_SEPARATOR . 'config.ini';
                if (!\file_exists($pathToConfigFile)) {
                    throw new \RuntimeException('Config file not exists');
                }

                return (new \Phalcon\Config\ConfigFactory())->load(
                    [
                        'filePath' => $pathToConfigFile,
                        'adapter' => 'ini',
                    ]
                );
            }
        );
    }

    /**
     * Initialize logger
     *
     * @param \Phalcon\Di\DiInterface $di
     */
    protected static function initializeLogger(\Phalcon\Di\DiInterface $di): void
    {
        /** @var \Phalcon\Config $config */
        $config = $di->get('config');
        $projectName = $config->basic->project_name;

        if ($projectName === null) {
           throw new \RuntimeException('Project name is not defined in configuration.');
        }

        $di->setShared(
            'logger',
            function () use ($projectName) {
                $adapter = new \Phalcon\Logger\Adapter\Syslog('PHP');
                $adapter->getFormatter()->setFormat("[{$projectName}][%type%] %message%");

                return new \Phalcon\Logger(
                    'messages',
                    [
                        'main' => $adapter,
                    ]
                );
            }
        );
    }

    /**
     * Initialize url component
     *
     * @param \Phalcon\Di\DiInterface $di
     */
    protected static function initializeUrl(\Phalcon\Di\DiInterface $di): void
    {
        $di->setShared(
            'url',
            function () {
                $url = new \Phalcon\Url();
                $url->setBaseUri('/');

                return $url;
            }
        );
    }

    /**
     * Initialize request
     *
     * @param \Phalcon\Di\DiInterface $di
     */
    protected static function initializeRequest(\Phalcon\Di\DiInterface $di): void
    {
        $di->setShared(
            'request',
            function () {
                return new \Phalcon\Http\Request();
            }
        );
    }

    /**
     * Initialize response
     *
     * @param \Phalcon\Di\DiInterface $di
     */
    protected static function initializeResponse(\Phalcon\Di\DiInterface $di): void
    {
        $di->setShared(
            'response',
            function () {
                $response = new \Phalcon\Http\Response();
                $response->setContentType('application/json');

                return $response;
            }
        );
    }

    /**
     * Initialize dispatcher
     *
     * @param \Phalcon\Di\DiInterface $di
     */
    protected static function initializeEventManager(\Phalcon\Di\DiInterface $di): void
    {
        $di->setShared(
            'eventsManager',
            function () {
                $eventsManager = new \Phalcon\Events\Manager();
                $authMiddleware = new \App\Middleware\Auth();
                $validationMiddleware = new \App\Middleware\Validation();
                $errorMiddleware = new \App\Middleware\Exception();
                $eventsManager->attach('dispatch:beforeExecuteRoute', $authMiddleware);
                $eventsManager->attach('dispatch:beforeExecuteRoute', $validationMiddleware);
                $eventsManager->attach('dispatch:beforeException', $errorMiddleware);

                return $eventsManager;
            }
        );
    }


    /**
     * Initialize dispatcher
     *
     * @param \Phalcon\Di\DiInterface $di
     */
    protected static function initializeDispatcher(\Phalcon\Di\DiInterface $di): void
    {
        $di->setShared(
            'dispatcher',
            function () use ($di) {
                $dispatcher = new \Phalcon\Mvc\Dispatcher();
                $dispatcher->setEventsManager($di->get('eventsManager'));

                return $dispatcher;
            }
        );
    }

    /**
     * @param \Phalcon\Di\DiInterface $di
     */
    protected static function initializeHttpValidation(\Phalcon\Di\DiInterface $di): void
    {
        $di->setShared(
            'dispatcher',
            function () use ($di) {
                return new \PayloadValidator\Validator\Factory();
            }
        );

        $di->setShared(
            'dispatcher',
            function () use ($di) {
                return new \PayloadValidator\Schema\Factory();
            }
        );
    }
}
