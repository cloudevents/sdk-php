<?php

namespace CloudEvents\Serializers\Exceptions;

use Throwable;

class UnsupportedEventSpecVersionException extends SerializationException
{
    public function __construct(
        string $message = 'Unsupported CloudEvent spec version.',
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
