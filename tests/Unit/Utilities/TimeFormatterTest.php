<?php

declare(strict_types=1);

namespace Tests\Unit\Utilities;

use CloudEvents\Utilities\TimeFormatter;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use ValueError;

class TimeFormatterTest extends TestCase
{
    public function testEncode(): void
    {
        self::assertEquals(
            '2018-04-05T17:31:00Z',
            TimeFormatter::encode(new DateTimeImmutable('2018-04-05T17:31:00Z'))
        );
    }

    public function testDecode(): void
    {
        self::assertEquals(
            new DateTimeImmutable('2018-04-05T17:31:00Z'),
            TimeFormatter::decode('2018-04-05T17:31:00Z')
        );
    }

    public function testEncodeEmpty(): void
    {
        self::assertEquals(
            null,
            TimeFormatter::encode(null)
        );
    }

    public function testDecodeEmpty(): void
    {
        self::assertEquals(
            null,
            TimeFormatter::decode(null)
        );
    }

    public function testDecodeInvalidTime(): void
    {
        $this->expectException(ValueError::class);

        $this->expectExceptionMessage(
            'CloudEvents\\Utilities\\TimeFormatter::decode(): Argument #1 ($time) is not a valid RFC3339 timestamp'
        );

        TimeFormatter::decode('2018asdsdsafd');
    }
}
