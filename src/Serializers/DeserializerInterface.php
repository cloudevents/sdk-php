<?php

declare(strict_types=1);

namespace CloudEvents\Serializers;

use CloudEvents\CloudEventInterface;
use CloudEvents\Exceptions\InvalidPayloadSyntaxException;
use CloudEvents\Exceptions\UnsupportedSpecVersionException;
use CloudEvents\Exceptions\MissingAttributeException;

interface DeserializerInterface
{
    /**
     * @throws InvalidPayloadSyntaxException
     * @throws UnsupportedSpecVersionException
     * @throws MissingAttributeException
     */
    public function deserializeStructured(string $payload): CloudEventInterface;

    /**
     * @throws InvalidPayloadSyntaxException
     * @throws UnsupportedSpecVersionException
     * @throws MissingAttributeException
     *
     * @return list<CloudEventInterface>
     */
    public function deserializeBatch(string $payload): array;

    /**
     * @param array<string, string> $attributes
     *
     * @throws InvalidPayloadSyntaxException
     * @throws UnsupportedSpecVersionException
     * @throws MissingAttributeException
     */
    public function deserializeBinary(string $data, string $contentType, array $attributes): CloudEventInterface;
}
