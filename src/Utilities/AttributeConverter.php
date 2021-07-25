<?php

declare(strict_types=1);

namespace CloudEvents\Utilities;

use CloudEvents\V1\CloudEventImmutable;
use CloudEvents\V1\CloudEventInterface;

/**
 * @internal
 */
final class AttributeConverter
{
    /**
     * @return array<string, bool|int|string>
     */
    public static function toArray(CloudEventInterface $cloudEvent): array
    {
        /** @var array<string, bool|int|string> */
        $attributes = array_filter([
            'specversion' => $cloudEvent->getSpecVersion(),
            'id' => $cloudEvent->getId(),
            'source' => $cloudEvent->getSource(),
            'type' => $cloudEvent->getType(),
            'datacontenttype' => $cloudEvent->getDataContentType(),
            'dataschema' => $cloudEvent->getDataSchema(),
            'subject' => $cloudEvent->getSubject(),
            'time' => TimeFormatter::encode($cloudEvent->getTime()),
        ], fn ($attr) => $attr !== null);

        return array_merge($attributes, $cloudEvent->getExtensions());
    }

    /**
     * @param array<array-key, mixed> $attributes
     */
    public static function fromArray(array $attributes): ?CloudEventImmutable
    {
        if (!isset($attributes['id']) || !isset($attributes['source']) || !isset($attributes['type'])) {
            return null;
        }

        /** @psalm-suppress MixedArgument */
        $cloudEvent = new CloudEventImmutable(
            $attributes['id'],
            $attributes['source'],
            $attributes['type']
        );

        /** @var mixed $value */
        foreach ($attributes as $attribute => $value) {
            switch ($attribute) {
                case 'specversion':
                case 'id':
                case 'source':
                case 'type':
                case 'data':
                case 'data_base64':
                    break;
                case 'datacontenttype':
                    /** @psalm-suppress MixedArgument */
                    $cloudEvent = $cloudEvent->withDataContentType($value);
                    break;
                case 'dataschema':
                    /** @psalm-suppress MixedArgument */
                    $cloudEvent = $cloudEvent->withDataSchema($value);
                    break;
                case 'subject':
                    /** @psalm-suppress MixedArgument */
                    $cloudEvent = $cloudEvent->withSubject($value);
                    break;
                case 'time':
                    /** @psalm-suppress MixedArgument */
                    $cloudEvent = $cloudEvent->withTime(TimeFormatter::decode($value));
                    break;
                default:
                    /** @psalm-suppress MixedArgument */
                    $cloudEvent = $cloudEvent->withExtension((string) $attribute, $value);
                    break;
            }
        }

        return $cloudEvent;
    }
}
