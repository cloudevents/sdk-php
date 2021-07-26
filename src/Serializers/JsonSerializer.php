<?php

declare(strict_types=1);

namespace CloudEvents\Serializers;

use CloudEvents\CloudEventInterface;
use CloudEvents\V1\CloudEventInterface as V1CloudEventInterface;
use CloudEvents\Exceptions\UnsupportedSpecVersionException;
use CloudEvents\Serializers\Normalizers\V1\Normalizer;
use CloudEvents\Serializers\Normalizers\V1\NormalizerInterface;

final class JsonSerializer implements SerializerInterface
{
    private NormalizerInterface $normalizer;

    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public static function create(): self
    {
        return new self(
            new Normalizer()
        );
    }

    /**
     * @throws UnsupportedSpecVersionException
     */
    public function serializeStructured(CloudEventInterface $cloudEvent): string
    {
        if (! $cloudEvent instanceof V1CloudEventInterface) {
            throw new UnsupportedSpecVersionException();
        }

        $normalized = $this->normalizer->normalize($cloudEvent, false);

        return json_encode($normalized);
    }

    /**
     * @param list<CloudEventInterface> $cloudEvents
     *
     * @throws UnsupportedSpecVersionException
     */
    public function serializeBatch(array $cloudEvents): string
    {
        $normalized = [];

        foreach ($cloudEvents as $cloudEvent) {
            if (! $cloudEvent instanceof V1CloudEventInterface) {
                throw new UnsupportedSpecVersionException();
            }

            $normalized[] = $this->normalizer->normalize($cloudEvent, false);
        }

        return json_encode($normalized);
    }

    /**
     * @throws UnsupportedSpecVersionException
     *
     * @return array{data: string, contentType: string, attributes: array<string, string>}
     */
    public function serializeBinary(CloudEventInterface $cloudEvent): array
    {
        if (! $cloudEvent instanceof V1CloudEventInterface) {
            throw new UnsupportedSpecVersionException();
        }

        $normalized = $this->normalizer->normalize($cloudEvent, true);

        /** @var string */
        $data = json_encode($normalized['data']);
        unset($normalized['data']);

        /** @var string */
        $contentType = $normalized['datacontenttype'] ?? 'application/json';
        unset($normalized['datacontenttype']);

        $attributes = [];

        /** @var mixed $value */
        foreach ($normalized as $key => $value) {
            $attributes[$key] = self::encodeAttributeValue($value);
        }

        return [
            'data' => $data,
            'contentType' => $contentType,
            'attributes' => $attributes,
        ];
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    private static function encodeAttributeValue($value): string
    {
        if ($value === true) {
            return 'true';
        }

        if ($value === false) {
            return 'false';
        }

        return rawurlencode((string) $value);
    }
}
