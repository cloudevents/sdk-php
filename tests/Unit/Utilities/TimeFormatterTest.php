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

    public function providesDecodeCases(): array
    {
        return [
            // UTC
            ['2018-04-05T17:31:00Z', '2018-04-05T17:31:00Z'],
            ['1985-04-12T23:20:50.100000Z', '1985-04-12T23:20:50.1Z'],
            ['1985-04-12T23:20:50.100000Z', '1985-04-12T23:20:50.10Z'],
            ['1985-04-12T23:20:50.100000Z', '1985-04-12T23:20:50.100Z'],
            ['1985-04-12T23:20:50.120000Z', '1985-04-12T23:20:50.12Z'],
            ['1985-04-12T23:20:50.120000Z', '1985-04-12T23:20:50.120Z'],
            ['1985-04-12T23:20:50.123000Z', '1985-04-12T23:20:50.123Z'],
            ['1985-04-12T23:20:50.123000Z', '1985-04-12T23:20:50.12300Z'],
            ['1985-04-12T23:20:50.123400Z', '1985-04-12T23:20:50.1234Z'],
            ['1985-04-12T23:20:50.123400Z', '1985-04-12T23:20:50.123400Z'],
            ['1985-04-12T23:20:50.123450Z', '1985-04-12T23:20:50.12345Z'],
            ['1985-04-12T23:20:50.123450Z', '1985-04-12T23:20:50.123450Z'],
            ['1985-04-12T23:20:50.123456Z', '1985-04-12T23:20:50.123456Z'],

            // +01:00
            ['2018-04-05T16:31:00Z', '2018-04-05T17:31:00+01:00'],
            ['1985-04-12T22:20:50.100000Z', '1985-04-12T23:20:50.1+01:00'],
            ['1985-04-12T22:20:50.100000Z', '1985-04-12T23:20:50.10+01:00'],
            ['1985-04-12T22:20:50.100000Z', '1985-04-12T23:20:50.100+01:00'],
            ['1985-04-12T22:20:50.120000Z', '1985-04-12T23:20:50.12+01:00'],
            ['1985-04-12T22:20:50.120000Z', '1985-04-12T23:20:50.120+01:00'],
            ['1985-04-12T22:20:50.123000Z', '1985-04-12T23:20:50.123+01:00'],
            ['1985-04-12T22:20:50.123000Z', '1985-04-12T23:20:50.12300+01:00'],
            ['1985-04-12T22:20:50.123400Z', '1985-04-12T23:20:50.1234+01:00'],
            ['1985-04-12T22:20:50.123400Z', '1985-04-12T23:20:50.123400+01:00'],
            ['1985-04-12T22:20:50.123450Z', '1985-04-12T23:20:50.12345+01:00'],
            ['1985-04-12T22:20:50.123450Z', '1985-04-12T23:20:50.123450+01:00'],
            ['1985-04-12T22:20:50.123456Z', '1985-04-12T23:20:50.123456+01:00'],
        ];
    }

    /**
     * @dataProvider providesDecodeCases
     */
    public function testDecode(string $expected, string $input): void
    {
        self::assertEquals(
            new DateTimeImmutable($expected),
            TimeFormatter::decode($input)
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
