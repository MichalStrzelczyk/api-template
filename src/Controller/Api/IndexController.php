<?php

declare (strict_types=1);

namespace App\Controller\Api;

class IndexController extends \Phalcon\Mvc\Controller
{
    /**
     * GET /api/status
     */
    public function statusAction()
    {
        $config = $this->getDI()->get('config');
        $projectName = isset($config['basic']['project_name']) ? $config['basic']['project_name'] : 'Undefined';
        $this->response->setContentType('application/json');
        $this->response->setContent(
            \json_encode(
                [
                    'status' => 'ok',
                    'projectName' => $projectName
                ]
            )
        );
    }

    /**
     * GET /api/{name}}
     */
    public function nameAction()
    {
        $this->response->setContent(
            \json_encode(
                [
                    'name' => $this->dispatcher->getParam('name')
                ]
            )
        );
    }
}
