<?php

namespace CloudEvents\Serializers\Exceptions;

use Throwable;

class UnsupportedEventSpecVersionException extends SerializationException
{
    public function __construct(
        $message = 'Unsupported CloudEvent spec version.',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
