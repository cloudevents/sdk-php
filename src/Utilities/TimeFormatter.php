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

        try {
            $decoded = new DateTimeImmutable($time);
        } catch (\Throwable $th) {
            throw new ValueError(
                \sprintf('%s(): Argument #1 ($time) is not a valid RFC3339 timestamp', __METHOD__)
            );
        }

        return self::shiftWithTimezone($time, $decoded);
    }

    private static function shiftWithTimezone(string $time, DateTimeImmutable $datetime): DateTimeImmutable
    {
        if (strpos($time, '+') === false && strpos($time, '-') === false && strtoupper(substr($time, -1)) !== 'Z') {
            return $datetime->setTimezone(new \DateTimeZone('UTC'));
        }

        return $datetime;
    }
}
