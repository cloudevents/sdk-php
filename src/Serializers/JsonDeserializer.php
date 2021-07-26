<?php

declare(strict_types=1);

namespace CloudEvents\Serializers;

use CloudEvents\CloudEventInterface;
use CloudEvents\Exceptions\InvalidPayloadSyntaxException;
use CloudEvents\Exceptions\UnsupportedSpecVersionException;
use CloudEvents\Exceptions\MissingAttributeException;
use CloudEvents\Serializers\Normalizers\V1\Denormalizer;
use CloudEvents\Serializers\Normalizers\V1\DenormalizerInterface;
use JsonException;

final class JsonDeserializer implements DeserializerInterface
{
    private DenormalizerInterface $denormalizer;

    public function __construct(DenormalizerInterface $denormalizer)
    {
        $this->denormalizer = $denormalizer;
    }

    public static function create(): self
    {
        return new self(
            new Denormalizer()
        );
    }

    /**
     * @throws InvalidPayloadSyntaxException
     * @throws UnsupportedSpecVersionException
     * @throws MissingAttributeException
     */
    public function deserializeStructured(string $payload): CloudEventInterface
    {
        try {
            /** @var mixed */
            $decoded = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InvalidPayloadSyntaxException(null, 0, $e);
        }

        if (!is_array($decoded)) {
            throw new InvalidPayloadSyntaxException();
        }

        return $this->denormalizer->denormalize($decoded);
    }

    /**
     * @throws InvalidPayloadSyntaxException
     * @throws UnsupportedSpecVersionException
     * @throws MissingAttributeException
     *
     * @return list<CloudEventInterface>
     */
    public function deserializeBatch(string $payload): array
    {
        try {
            $decoded = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InvalidPayloadSyntaxException(null, 0, $e);
        }

        if (!is_array($decoded)) {
            throw new InvalidPayloadSyntaxException();
        }

        $cloudEvents = [];

        foreach ($decoded as $value) {
            if (!is_array($value)) {
                throw new InvalidPayloadSyntaxException();
            }

            $cloudEvents[] = $this->denormalizer->denormalize($value);
        }

        return $cloudEvents;
    }

    /**
     * @param array<string, string> $attributes
     *
     * @throws InvalidPayloadSyntaxException
     * @throws UnsupportedSpecVersionException
     * @throws MissingAttributeException
     */
    public function deserializeBinary(string $data, string $contentType, array $attributes): CloudEventInterface
    {
        try {
            /** @var mixed */
            $decoded = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InvalidPayloadSyntaxException(null, 0, $e);
        }

        return $this->denormalizer->denormalize(
            array_merge(
                array_map('rawurldecode', $attributes),
                ['data' => $decoded, 'datacontenttype' => $contentType]
            )
        );
    }
}
