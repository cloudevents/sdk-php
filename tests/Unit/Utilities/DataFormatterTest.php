<?php

declare(strict_types=1);

namespace Tests\Unit\Utilities;

use CloudEvents\Utilities\DataFormatter;
use PHPUnit\Framework\TestCase;
use ValueError;

class DataFormatterTest extends TestCase
{
    public function testEncodeAndDecodeSimple(): void
    {
        self::assertEquals(['data' => ['foo' => 'bar']], DataFormatter::encode(['foo' => 'bar'], false));
        self::assertEquals(['foo' => 'bar'], DataFormatter::decode(['data' => ['foo' => 'bar']]));
    }

    public function testEncodeAndDecodeBinary(): void
    {
        $data = random_bytes(1024);
        $encoded = base64_encode($data);

        self::assertEquals(['data_base64' => $encoded], DataFormatter::encode($data, false));
        self::assertEquals($data, DataFormatter::decode(['data_base64' => $encoded]));

        self::assertEquals(['data' => $data], DataFormatter::encode($data, true));
        self::assertEquals($data, DataFormatter::decode(['data' => $data]));
    }

    public function testInvalidBase64Decode1(): void
    {
        $this->expectException(ValueError::class);

        DataFormatter::decode(['data_base64' => 123]);
    }

    public function testInvalidBase64Decode2(): void
    {
        $this->expectException(ValueError::class);

        DataFormatter::decode(['data_base64' => 'a']);
    }

    public function testEncodeAndDecodeEmpty(): void
    {
        self::assertEquals([], DataFormatter::encode(null, false));
        self::assertEquals(null, DataFormatter::decode([]));
    }
}
