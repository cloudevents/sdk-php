<?php

declare(strict_types=1);

namespace CloudEvents\Exceptions;

use Exception;
use Throwable;

class MissingAttributeException extends Exception
{
    public function __construct(
        string $message = null,
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message ?? 'Missing CloudEvent attribute.', $code, $previous);
    }
}
