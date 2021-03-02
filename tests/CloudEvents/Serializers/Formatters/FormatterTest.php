<?php

namespace CloudEvents\Tests\CloudEvents\Serializers\Formatters;

use CloudEvents\Serializers\Formatters\Formatter;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \CloudEvents\Serializers\Formmaters\Formatter
 */
class FormatterTest extends TestCase
{
    /**
     * @covers ::encodeTime
     * @covers ::decodeTime
     */
    public function testTime(): void
    {
        $formatter = new Formatter();
        $this->assertEquals('2018-04-05T17:31:00Z', $formatter->encodeTime(new DateTimeImmutable('2018-04-05T17:31:00Z')));
        $this->assertEquals(new DateTimeImmutable('2018-04-05T17:31:00Z'), $formatter->decodeTime('2018-04-05T17:31:00Z'));
    }

    /**
     * @covers ::encodeData
     * @covers ::decodeData
     */
    public function testData(): void
    {
        $formatter = new Formatter();
        $data = random_bytes(1024);
        $encoded = base64_encode($data);
        $this->assertEquals(['data' => ['foo' => 'bar']], $formatter->encodeData(['foo' => 'bar']));
        $this->assertEquals(['foo' => 'bar'], $formatter->decodeData(['data' => ['foo' => 'bar']]));
        $this->assertEquals(['data_base64' => $encoded], $formatter->encodeData($data));
        $this->assertEquals($data, $formatter->decodeData(['data_base64' => $encoded]));
    }
}
