<?php

declare(strict_types=1);

namespace CloudEvents\Serializers\Normalizers\V1;

use CloudEvents\Exceptions\InvalidAttributeException;
use CloudEvents\Exceptions\MissingAttributeException;
use CloudEvents\Exceptions\UnsupportedSpecVersionException;
use CloudEvents\V1\CloudEventInterface;

interface NormalizerInterface
{
    /**
     * @return array<string, mixed>
     */
    public function normalize(CloudEventInterface $cloudEvent, bool $rawData): array;
}
