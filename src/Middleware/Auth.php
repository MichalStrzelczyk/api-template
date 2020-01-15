<?php

declare (strict_types=1);

namespace App\Middleware;

class Auth
{
    /**
     * @param \Phalcon\Events\Event $event
     * @param \Phalcon\Mvc\Dispatcher $dispatcher
     *
     * @return bool
     */
    public function beforeExecuteRoute(\Phalcon\Events\Event $event, \Phalcon\Mvc\Dispatcher $dispatcher): bool
    {
        $acl = $dispatcher->getDI()->get('acl');
        $requestKey = $this->createRequestKey($dispatcher);

        if (!\array_key_exists($requestKey, $acl)) {
            // This route has no ACL validation => open access
            return true;
        }

        $aclEntry = $acl[$requestKey];
        $userPermission = $this->getPermissions($aclEntry, $dispatcher->getDI());

        $permChecker = $dispatcher->getDI()->get('Service\PermissionsChecker');
        $permChecker->setPrivileges($userPermission);
        $resourcePermissions = $aclEntry['permissions'];

        foreach ($resourcePermissions as $permissionEntry) {
            $systemName = $permissionEntry[0] ?? '*';
            $elementType = $permissionEntry[1] ?? '*';
            $elementId = $permissionEntry[2] ?? '*';
            $component = $permissionEntry[3] ?? '*';
            $flag = $permissionEntry[4] ?? -1;

            // Check if permissions are dynamic (from request)
            if (\strpos($systemName, '$') !== false) {
                $systemName = \ltrim($systemName,'$');
                $systemName = $dispatcher->getParam($systemName);
            }

            if (\strpos($elementType, '$') !== false) {
                $elementType = \ltrim($elementType,'$');
                $elementType = $dispatcher->getParam($elementType);
            }

            if (\strpos($elementId, '$') !== false) {
                $elementId = \ltrim($elementId,'$');
                $elementId = $dispatcher->getParam($elementId);
            }

            $hasAccess = $permChecker->check($systemName, $elementType, $elementId, $component, $flag);
            if ($hasAccess) {
                return true;
            }
        }

        throw new \App\Exception\Http\Unauthorized();
    }

    /**
     * @return string
     */
    private function createRequestKey(\Phalcon\Mvc\Dispatcher $dispatcher): string
    {
        $httpMethod = \strtolower($dispatcher->getDi()->get('request')->getMethod());
        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();
        $namespace = \mb_strtolower($dispatcher->getNamespaceName());

        return $httpMethod . '::' . $namespace . '::' .$controller . '::' . $action;
    }

    /**
     * @param array $aclEntry
     * @param \Phalcon\Di $di
     *
     * @return array
     */
    private function getPermissions(array $aclEntry, \Phalcon\Di $di): array
    {
        $aclAdapterName = $di->get('config')->basic->acl_adapter;

        if($aclAdapterName === null) {
            throw new \RuntimeException('acl_adapter is not defined in config file.');
        }

        switch (\mb_strtolower($aclAdapterName)) {
            case 'session':
                /** @var \Phalcon\Session\Manager $sessionManager */
                if ($di->get('session')->has('user')) {
                    return $di->get('session')->get('user')->getPrivileges();
                }
        }

        return [];
    }
}
