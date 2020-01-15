<?php

declare (strict_types=1);

namespace App\Controller;

class TestController extends \Phalcon\Mvc\Controller
{
    /**
     * GET /test/{name}?age=10&limit=10
     */
    public function nameAction()
    {
        $this->response->setContent(
            \json_encode(
                [
                    'status' => true,
                    'data' => $this->request->parametersAfterValidation
                ]
            )
        );
    }
}
