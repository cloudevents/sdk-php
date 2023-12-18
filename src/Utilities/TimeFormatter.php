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
    private const TIME_FORMAT = 'Y-m-d\TH:i:s';
    private const TIME_FORMAT_EXTENDED = 'Y-m-d\TH:i:s.u';
    private const TIME_ZONE = 'UTC';

    private const RFC3339_FORMAT = 'Y-m-d\TH:i:sP';
    private const RFC3339_EXTENDED_FORMAT = 'Y-m-d\TH:i:s.uP';

    /**
     * @param int<0, 6> $subsecondPrecision
     */
    public static function encode(?DateTimeImmutable $time, int $subsecondPrecision): ?string
    {
        if ($time === null) {
            return null;
        }

        return sprintf('%sZ', self::encodeWithoutTimezone($time, $subsecondPrecision));
    }

    /**
     * @param int<0, 6> $subsecondPrecision
     */
    private static function encodeWithoutTimezone(DateTimeImmutable $time, int $subsecondPrecision): string
    {
        $utcTime = $time->setTimezone(new DateTimeZone(self::TIME_ZONE));

        if ($subsecondPrecision <= 0) {
            return $utcTime->format(self::TIME_FORMAT);
        }

        if ($subsecondPrecision >= 6) {
            return $utcTime->format(self::TIME_FORMAT_EXTENDED);
        }

        return substr($utcTime->format(self::TIME_FORMAT_EXTENDED), 0, $subsecondPrecision - 6);
    }

    public static function decode(?string $time): ?DateTimeImmutable
    {
        if ($time === null) {
            return null;
        }

        $time = \strtoupper($time);

        /** @psalm-suppress UndefinedFunction */
        $decoded = \str_contains($time, '.')
            ? DateTimeImmutable::createFromFormat(self::RFC3339_EXTENDED_FORMAT, self::truncateOverPrecision($time), new DateTimeZone(self::TIME_ZONE))
            : DateTimeImmutable::createFromFormat(self::RFC3339_FORMAT, $time, new DateTimeZone(self::TIME_ZONE));

        if ($decoded === false) {
            throw new ValueError(
                \sprintf('%s(): Argument #1 ($time) is not a valid RFC3339 timestamp', __METHOD__)
            );
        }

        return $decoded;
    }

    private static function truncateOverPrecision(string $time): string
    {
        [$fst, $snd] = explode('.', $time);

        // match the first n digits at the start
        \preg_match('/^\d+/', $snd, $matches);

        $digits = $matches[0] ?? '';

        // datetime portion + period + up to 6 digits + timezone string
        return $fst . '.' . substr($digits, 0, 6) . substr($snd, strlen($digits));
    }
}
