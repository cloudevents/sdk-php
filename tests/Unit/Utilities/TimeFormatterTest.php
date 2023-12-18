<?php

declare(strict_types=1);

namespace Tests\Unit\Utilities;

use CloudEvents\Utilities\TimeFormatter;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use ValueError;

class TimeFormatterTest extends TestCase
{
    public static function providesValidEncodeCases(): array
    {
        return [
            ['2018-04-05T17:31:00Z', '2018-04-05T17:31:00.123456Z', 0],
            ['2018-04-05T17:31:00.1Z', '2018-04-05T17:31:00.123456Z', 1],
            ['2018-04-05T17:31:00.12Z', '2018-04-05T17:31:00.123456Z', 2],
            ['2018-04-05T17:31:00.123Z', '2018-04-05T17:31:00.123456Z', 3],
            ['2018-04-05T17:31:00.1234Z', '2018-04-05T17:31:00.123456Z', 4],
            ['2018-04-05T17:31:00.12345Z', '2018-04-05T17:31:00.123456Z', 5],
            ['2018-04-05T17:31:00.123456Z', '2018-04-05T17:31:00.123456Z', 6],
        ];
    }

    /**
     * @dataProvider providesValidEncodeCases
     */
    public function testEncode(string $expected, string $input, int $subsecondPrecision): void
    {
        self::assertEquals(
            $expected,
            TimeFormatter::encode(new DateTimeImmutable($input), $subsecondPrecision)
        );
    }

    public static function providesValidDecodeCases(): array
    {
        return [
            // UTC
            ['2018-04-05T17:31:00Z', '2018-04-05t17:31:00Z'],
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
            ['1985-04-12T23:20:50.123456Z', '1985-04-12T23:20:50.1234567Z'],
            ['1985-04-12T23:20:50.123456Z', '1985-04-12T23:20:50.12345678Z'],
            ['1985-04-12T23:20:50.123456Z', '1985-04-12T23:20:50.123456789Z'],

            // +01:00
            ['2018-04-05T16:31:00Z', '2018-04-05t17:31:00+01:00'],
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
            ['1985-04-12T22:20:50.123456Z', '1985-04-12T23:20:50.1234567+01:00'],
            ['1985-04-12T22:20:50.123456Z', '1985-04-12T23:20:50.12345678+01:00'],
            ['1985-04-12T22:20:50.123456Z', '1985-04-12T23:20:50.123456789+01:00'],

            // -05:00
            ['2018-04-05T22:31:00Z', '2018-04-05t17:31:00-05:00'],
            ['2018-04-05T22:31:00Z', '2018-04-05T17:31:00-05:00'],
            ['1985-04-13T04:20:50.123456Z', '1985-04-12T23:20:50.123456-05:00'],
            ['1985-04-13T04:20:50.123456Z', '1985-04-12T23:20:50.123456789-05:00'],
        ];
    }

    /**
     * @dataProvider providesValidDecodeCases
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
            TimeFormatter::encode(null, 0)
        );
    }

    public function testDecodeEmpty(): void
    {
        self::assertEquals(
            null,
            TimeFormatter::decode(null)
        );
    }

    public static function providesInvalidDecodeCases(): array
    {
        return [
            [''],
            ['123'],
            ['2018asdsdsafd'],
            ['2018-04-05'],
            ['2018-04-05 17:31:00Z'],
            ['2018-04-05T17:31:00.Z'],
            ['2018-04-05T17:31:00ZZ'],
        ];
    }

    /**
     * @dataProvider providesInvalidDecodeCases
     */
    public function testDecodeInvalidTime(string $input): void
    {
        $this->expectException(ValueError::class);

        $this->expectExceptionMessage(
            'CloudEvents\\Utilities\\TimeFormatter::decode(): Argument #1 ($time) is not a valid RFC3339 timestamp'
        );

        TimeFormatter::decode($input);
    }
}
