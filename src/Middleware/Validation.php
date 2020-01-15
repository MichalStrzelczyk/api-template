<?php

declare (strict_types=1);

namespace App\Middleware;

class Validation
{
    use \App\Request\KeyGeneratorTrait;

    /**
     * @param \Phalcon\Events\Event $event
     * @param \Phalcon\Mvc\Dispatcher $dispatcher
     *
     * @return bool
     */
    public function beforeExecuteRoute(\Phalcon\Events\Event $event, \Phalcon\Mvc\Dispatcher $dispatcher): bool
    {
        /** @var \Phalcon\Http\Request $request */
        $request = $dispatcher->getDI()->get('request');
        $validation = $dispatcher->getDI()->get('validation');
        $requestKey = $this->createRequestKey($dispatcher, $request);

        if (!\array_key_exists($requestKey, $validation)) {
            // This route has no parameters validation
            return true;
        }

        $schema = $dispatcher->getDI()->get('PayloadValidator\SchemaFactory')->createFromArray($validation[$requestKey]);
        $data = (object) $this->createRequestData($dispatcher, $request);
        $validator = $dispatcher->getDI()->get('PayloadValidator\ValidatorFactory')->create();

        $validator->schemaValidation($data, $schema, 9999);
        $errorContainer = $validator->getErrorContainer();
        $request->parametersAfterValidation = (array) $data;

        if (\count($errorContainer) > 0 ) {
            throw new \App\Exception\Http\BadRequest($errorContainer);
        }

        return true;
    }

}
