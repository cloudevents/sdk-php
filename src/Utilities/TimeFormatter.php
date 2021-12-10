<?php

declare(strict_types=1);

namespace CloudEvents\Utilities;

use DateTimeImmutable;
use DateTimeZone;
use ValueError;

/**
 * @internal
 */
final class TimeFormatter
{
    private const TIME_FORMAT = 'Y-m-d\TH:i:s\Z';
    private const TIME_ZONE = 'UTC';

    private const RFC3339_FORMAT = 'Y-m-d\TH:i:sP';
    private const RFC3339_EXTENDED_FORMAT = 'Y-m-d\TH:i:s.uP';

    public static function encode(?DateTimeImmutable $time): ?string
    {
        if ($time === null) {
            return null;
        }

        return $time->setTimezone(new DateTimeZone(self::TIME_ZONE))->format(self::TIME_FORMAT);
    }

    public static function decode(?string $time): ?DateTimeImmutable
    {
        if ($time === null) {
            return null;
        }

        $decoded = DateTimeImmutable::createFromFormat(
            \str_contains($time, '.') ? self::RFC3339_EXTENDED_FORMAT : self::RFC3339_FORMAT,
            \strtoupper($time),
            new DateTimeZone(self::TIME_ZONE)
        );

        if ($decoded === false) {
            throw new ValueError(
                \sprintf('%s(): Argument #1 ($time) is not a valid RFC3339 timestamp', __METHOD__)
            );
        }

        return $decoded;
    }
}
