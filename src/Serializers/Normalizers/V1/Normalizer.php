<?php

declare(strict_types=1);

namespace CloudEvents\Serializers\Normalizers\V1;

use CloudEvents\V1\CloudEventInterface;
use CloudEvents\Utilities\AttributeConverter;
use CloudEvents\Utilities\DataFormatter;

final class Normalizer implements NormalizerInterface
{
    /**
     * @return array<string, mixed>
     */
    public function normalize(CloudEventInterface $cloudEvent, bool $rawData): array
    {
        return array_merge(
            AttributeConverter::toArray($cloudEvent),
            DataFormatter::encode($cloudEvent->getData(), $rawData)
        );
    }
}
