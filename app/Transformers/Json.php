<?php

namespace App\Transformers;

class Json
{
    public static function response($error = false, $message = null): array
    {
        return [
            'error'    => $error,
            'message' => $message,
        ];
    }
}
