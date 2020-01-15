<?php

declare (strict_types=1);

namespace App\Process\Auth\Validator;

class Logout implements \Process\ValidatorInterface
{
    /**
     * @param array $data
     *
     * @return array
     */
    public function validate(array $data = []): array
    {
        return [];
    }
}
