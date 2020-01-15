<?php

declare (strict_types=1);

namespace App\Di;

use \Miinto\AuthService\Sdk;

/** @var \Phalcon\Di $di */

$di->setShared(
    'Service\HttpClient',
    function () use ($di) {
        return \Http\Discovery\HttpClientDiscovery::find();
    }
);

$di->setShared(
    'Service\AuthClient',
    function () use ($di) {
        if (!isset($di->get('config')->basic->auth_service_api_url)) {
            throw new \RuntimeException('Parameter `auth_service_api_url` is not defined in config file');
        }

        $urlToApi = $di->get('config')->basic->auth_service_api_url;

        if (!$di->has('Service\HttpClient')) {
            throw new \RuntimeException('Service\HttpClient must be inject to DI Container');
        }

        $httpClient = $di->get('Service\HttpClient');
        $requestFactory = new \Phalcon\Http\Message\ServerRequestFactory();
        $basicClient = Sdk\Http\Client\Factory::createBasicClient($urlToApi, $httpClient, $requestFactory);

        return Sdk\Factory::createAuthService($basicClient);
    }
);

$di->setShared(
    'Service\PermissionsChecker',
    function () use ($di) {
        return new Sdk\PermissionsChecker();
    }
);
