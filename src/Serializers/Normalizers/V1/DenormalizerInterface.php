<?php

declare(strict_types=1);

namespace CloudEvents\Serializers\Normalizers\V1;

use CloudEvents\Exceptions\InvalidAttributeException;
use CloudEvents\Exceptions\MissingAttributeException;
use CloudEvents\Exceptions\UnsupportedSpecVersionException;
use CloudEvents\V1\CloudEventInterface;

interface DenormalizerInterface
{
    /**
     * @param array<array-key, mixed> $payload
     *
     * @throws InvalidAttributeException
     * @throws UnsupportedSpecVersionException
     * @throws MissingAttributeException
     */
    public function denormalize(array $payload): CloudEventInterface;
}
