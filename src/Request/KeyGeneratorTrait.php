<?php

declare (strict_types=1);

namespace App\Request;

trait KeyGeneratorTrait
{
    /**
     * @param \Phalcon\Mvc\Dispatcher $dispatcher
     * @param \Phalcon\Http\Request $request
     *
     * @return string
     */
    private function createRequestKey(\Phalcon\Mvc\Dispatcher $dispatcher, \Phalcon\Http\Request $request): string
    {
        $httpMethod = \strtolower($request->getMethod());
        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();
        $namespace = \mb_strtolower($dispatcher->getNamespaceName());

        return $httpMethod . '::' . $namespace . '::' .$controller . '::' . $action;
    }

    /**
     * @param \Phalcon\Mvc\Dispatcher $dispatcher
     * @param \Phalcon\Http\Request $request
     *
     * @return array
     */
    private function createRequestData(\Phalcon\Mvc\Dispatcher $dispatcher, \Phalcon\Http\Request $request): array
    {
        return \array_merge(
            // Parameters from query
            $request->getFilteredQuery(),

            // Parameters from body
            $request->getFilteredPost(),

            // Parameters from routes
            $dispatcher->getParams()
        );
    }
}
