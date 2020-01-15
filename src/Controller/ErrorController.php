<?php

declare (strict_types=1);

namespace App\Controller;

class ErrorController extends \Phalcon\Mvc\Controller
{
    /**
     * GET /api/404
     */
    public function notFoundAction()
    {
        throw new \App\Exception\Http\NotFound([],'Page not found');
    }

    /**
     * GET /api/500
     */
    public function errorAction()
    {
        throw new \App\Exception\Http\Basic([],'Application error');
    }

    /**
     * GET /api/401
     */
    public function permissionErrorAction()
    {
        throw new \App\Exception\Http\Unauthorized([],'Access denied');
    }
}
