<?php

namespace CloudEvents\Tests\CloudEvents\Serializers;

use CloudEvents\Serializers\ArraySerializer;
use CloudEvents\V1\CloudEventInterface;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \CloudEvents\Serializers\ArraySerializer
 */
class ArraySerializerTest extends TestCase
{
    /**
     * @covers ::serialize
     */
    public function testSerialize(): void
    {
        /** @var CloudEventInterface|Stub $event */
        $event = $this->createStub(CloudEventInterface::class);
        $event->method('getSpecVersion')->willReturn('1.0');
        $event->method('getId')->willReturn('1234-1234-1234');
        $event->method('getType')->willReturn('com.example.someevent');
        $event->method('getDataContentType')->willReturn('application/json');
        $event->method('getDataSchema')->willReturn('com.example/schema');
        $event->method('getSubject')->willReturn('larger-context');
        $event->method('getTime')->willReturn(new DateTimeImmutable('2018-04-05T17:31:00Z'));
        $event->method('getData')->willReturn(['key' => 'value']);

        $formatter = new ArraySerializer();

        $this->assertSame(
            [
                'specversion' => '1.0',
                'id' => '1234-1234-1234',
                'type' => 'com.example.someevent',
                'datacontenttype' => 'application/json',
                'dataschema' => 'com.example/schema',
                'subject' => 'larger-context',
                'time' => '2018-04-05T17:31:00Z',
                'data' => [
                    'key' => 'value',
                ]
            ],
            $formatter->serialize($event)
        );
    }
}
