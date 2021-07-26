<?php

declare(strict_types=1);

namespace CloudEvents\Serializers\Normalizers\V1;

use CloudEvents\Exceptions\InvalidAttributeException;
use CloudEvents\Exceptions\MissingAttributeException;
use CloudEvents\Exceptions\UnsupportedSpecVersionException;
use CloudEvents\V1\CloudEventInterface;
use Throwable;
use CloudEvents\Utilities\AttributeConverter;
use CloudEvents\Utilities\DataFormatter;

final class Denormalizer implements DenormalizerInterface
{
    /**
     * @param array<array-key, mixed> $payload
     *
     * @throws InvalidAttributeException
     * @throws UnsupportedSpecVersionException
     * @throws MissingAttributeException
     */
    public function denormalize(array $payload): CloudEventInterface
    {
        try {
            if (($payload['specversion'] ?? null) !== CloudEventInterface::SPEC_VERSION) {
                throw new UnsupportedSpecVersionException();
            }

            $cloudEvent = AttributeConverter::fromArray($payload);

            if ($cloudEvent === null) {
                throw new MissingAttributeException();
            }

            return $cloudEvent->withData(DataFormatter::decode($payload));
        } catch (Throwable $e) {
            if ($e instanceof UnsupportedSpecVersionException || $e instanceof MissingAttributeException) {
                throw $e;
            }

            throw new InvalidAttributeException(null, 0, $e);
        }
    }
}
