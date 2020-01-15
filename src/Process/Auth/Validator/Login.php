<?php

declare (strict_types=1);

namespace App\Process\Auth\Validator;

class Login implements \Process\ValidatorInterface
{
    /**
     * @param array $data
     *
     * @return array
     */
    public function validate(array $data = []): array
    {
        $errors = [];

        if (!isset($data['login']) || strlen($data['login']) === 0) {
            $errors['1001'] = 'loginIsMissing';
        }

        if (!isset($data['password']) || strlen($data['password']) === 0) {
            $errors['1002'] = 'passwordIsMissing';
        }

        return $errors;
    }
}
