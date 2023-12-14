<?php

declare(strict_types=1);

namespace CloudEvents\Serializers\Normalizers\V1;

use CloudEvents\V1\CloudEventInterface;
use CloudEvents\Utilities\AttributeConverter;
use CloudEvents\Utilities\DataFormatter;

final class Normalizer implements NormalizerInterface
{
    /**
     * @var array{subsecondPrecision?: int<0, 6>}
     */
    private array $configuration;

    /**
     * @param array{subsecondPrecision?: int<0, 6>} $configuration
     */
    public function __construct(array $configuration = [])
    {
        $this->configuration = $configuration;
    }

    /**
     * @return array<string, mixed>
     */
    public function normalize(CloudEventInterface $cloudEvent, bool $rawData): array
    {
        return array_merge(
            AttributeConverter::toArray($cloudEvent, $this->configuration),
            DataFormatter::encode($cloudEvent->getData(), $rawData)
        );
    }
}
