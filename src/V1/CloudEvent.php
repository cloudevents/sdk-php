<?php

declare(strict_types=1);

namespace CloudEvents\V1;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class CloudEvent implements CloudEventInterface
{
    use CloudEventTrait {
        setId as public;
        setSource as public;
        setType as public;
        setData as public;
        setDataContentType as public;
        setDataSchema as public;
        setSubject as public;
        setTime as public;
        setExtensions as public;
        setExtension as public;
    }
}
