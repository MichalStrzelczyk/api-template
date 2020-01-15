<?php

declare (strict_types=1);

namespace App\Middleware;

class Exception
{
    /**
     * @param \Phalcon\Events\Event $event
     * @param \Phalcon\Mvc\Dispatcher $dispatcher
     *
     * @return bool
     */
    public function beforeException(\Phalcon\Events\Event $event, \Phalcon\Mvc\Dispatcher $dispatcher): bool
    {
        if ($event->getData() instanceof \App\Exception\Http\Basic) {
            $data['status'] = false;
            $data['message'] = $event->getData()->getMessage();
            $data['errors'] = $event->getData()->getErrorContainer();

            $response = $dispatcher->getDI()->get('response');
            $response->setContent(\json_encode($data));
            $response->setStatusCode($event->getData()->getHttpErrorCode());

            return false;
        }

        return true;
    }
}
