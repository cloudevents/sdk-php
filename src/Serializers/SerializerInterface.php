<?php

declare(strict_types=1);

namespace CloudEvents\Serializers;

use CloudEvents\CloudEventInterface;
use CloudEvents\Exceptions\UnsupportedSpecVersionException;

interface SerializerInterface
{
    /**
     * @throws UnsupportedSpecVersionException
     */
    public function serializeStructured(CloudEventInterface $cloudEvent): string;

    /**
     * @param list<CloudEventInterface> $cloudEvents
     *
     * @throws UnsupportedSpecVersionException
     */
    public function serializeBatch(array $cloudEvents): string;

    /**
     * @throws UnsupportedSpecVersionException
     *
     * @return array{data: string, contentType: string, attributes: array<string, string>}
     */
    public function serializeBinary(CloudEventInterface $cloudEvent): array;
}
