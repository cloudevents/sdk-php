<?php

declare(strict_types=1);

namespace CloudEvents\Exceptions;

use Exception;
use Throwable;

class UnsupportedSpecVersionException extends Exception
{
    public function __construct(
        string $message = null,
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message ?? 'Unsupported CloudEvent spec version.', $code, $previous);
    }
}
