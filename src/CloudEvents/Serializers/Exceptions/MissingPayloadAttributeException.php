<?php

namespace CloudEvents\Serializers\Exceptions;

use Throwable;

class MissingPayloadAttributeException extends SerializationException
{
    public function __construct(
        string $message = 'Missing payload attribute.',
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
