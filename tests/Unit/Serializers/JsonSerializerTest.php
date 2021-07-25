<?php

declare(strict_types=1);

namespace Tests\Unit\Serializers;

use CloudEvents\CloudEventInterface;
use CloudEvents\Serializers\JsonSerializer;
use CloudEvents\Serializers\SerializerInterface;
use CloudEvents\Serializers\Normalizers\V1\NormalizerInterface;
use CloudEvents\V1\CloudEventInterface as CloudEventInterfaceV1;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use CloudEvents\Exceptions\UnsupportedSpecVersionException;

class JsonSerializerTest extends TestCase
{
    public function testInstantiate(): void
    {
        self::assertInstanceOf(
            SerializerInterface::class,
            new JsonSerializer($this->createStub(NormalizerInterface::class))
        );

        self::assertInstanceOf(
            SerializerInterface::class,
            JsonSerializer::create()
        );
    }

    public function testSerializeStructured(): void
    {
        /** @var CloudEventInterfaceV1|Stub $event */
        $event = $this->createStub(CloudEventInterfaceV1::class);
        $event->method('getSpecVersion')->willReturn('1.0');
        $event->method('getId')->willReturn('1234-1234-1234');
        $event->method('getSource')->willReturn('/var/data');
        $event->method('getType')->willReturn('com.example.someevent');
        $event->method('getDataContentType')->willReturn('application/json');
        $event->method('getDataSchema')->willReturn('com.example/schema');
        $event->method('getSubject')->willReturn('larger-context');
        $event->method('getTime')->willReturn(new DateTimeImmutable('2018-04-05T17:31:00Z'));
        $event->method('getData')->willReturn(['key' => 'value']);
        $event->method('getExtensions')->willReturn(['comacme' => 'foo']);

        $formatter = JsonSerializer::create();

        self::assertSame(
            '{"specversion":"1.0","id":"1234-1234-1234","source":"\/var\/data","type":"com.example.someevent","datacontenttype":"application\/json","dataschema":"com.example\/schema","subject":"larger-context","time":"2018-04-05T17:31:00Z","comacme":"foo","data":{"key":"value"}}',
            $formatter->serializeStructured($event)
        );
    }

    public function testSerializeStructuredUnsupportedSpecVersion(): void
    {
        /** @var CloudEventInterface|Stub $event */
        $event = $this->createStub(CloudEventInterface::class);
        $event->method('getSpecVersion')->willReturn('0.3');

        $formatter = JsonSerializer::create();

        $this->expectException(UnsupportedSpecVersionException::class);

        $formatter->serializeStructured($event);
    }

    public function testSerializeBatch(): void
    {
        /** @var CloudEventInterfaceV1|Stub $event1 */
        $event1 = $this->createStub(CloudEventInterfaceV1::class);
        $event1->method('getSpecVersion')->willReturn('1.0');
        $event1->method('getId')->willReturn('1234-1234-1234');
        $event1->method('getSource')->willReturn('/var/data');
        $event1->method('getType')->willReturn('com.example.someevent');
        $event1->method('getDataContentType')->willReturn('application/json');
        $event1->method('getDataSchema')->willReturn('com.example/schema');
        $event1->method('getSubject')->willReturn('larger-context');
        $event1->method('getTime')->willReturn(new DateTimeImmutable('2018-04-05T17:31:00Z'));
        $event1->method('getData')->willReturn(['key' => 'value']);
        $event1->method('getExtensions')->willReturn(['comacme' => 'foo']);

        /** @var CloudEventInterfaceV1|Stub $event2 */
        $event2 = $this->createStub(CloudEventInterfaceV1::class);
        $event2->method('getSpecVersion')->willReturn('1.0');
        $event2->method('getId')->willReturn('1234-1234-2222');
        $event2->method('getSource')->willReturn('/var/data');
        $event2->method('getType')->willReturn('com.example.someevent');
        $event2->method('getDataContentType')->willReturn('application/json');
        $event2->method('getDataSchema')->willReturn('com.example/schema');
        $event2->method('getSubject')->willReturn('larger-context');
        $event2->method('getTime')->willReturn(new DateTimeImmutable('2018-04-05T17:31:00Z'));
        $event2->method('getData')->willReturn(['key' => 'value']);
        $event2->method('getExtensions')->willReturn(['comacme' => 'foo']);

        $formatter = JsonSerializer::create();

        self::assertSame(
            '[{"specversion":"1.0","id":"1234-1234-1234","source":"\/var\/data","type":"com.example.someevent","datacontenttype":"application\/json","dataschema":"com.example\/schema","subject":"larger-context","time":"2018-04-05T17:31:00Z","comacme":"foo","data":{"key":"value"}},{"specversion":"1.0","id":"1234-1234-2222","source":"\/var\/data","type":"com.example.someevent","datacontenttype":"application\/json","dataschema":"com.example\/schema","subject":"larger-context","time":"2018-04-05T17:31:00Z","comacme":"foo","data":{"key":"value"}}]',
            $formatter->serializeBatch([$event1, $event2])
        );
    }

    public function testSerializeBatchUnsupportedSpecVersion(): void
    {
        /** @var CloudEventInterfaceV1|Stub $event1 */
        $event1 = $this->createStub(CloudEventInterfaceV1::class);
        $event1->method('getSpecVersion')->willReturn('1.0');
        $event1->method('getId')->willReturn('1234-1234-1234');
        $event1->method('getSource')->willReturn('/var/data');
        $event1->method('getType')->willReturn('com.example.someevent');
        $event1->method('getDataContentType')->willReturn('application/json');
        $event1->method('getDataSchema')->willReturn('com.example/schema');
        $event1->method('getSubject')->willReturn('larger-context');
        $event1->method('getTime')->willReturn(new DateTimeImmutable('2018-04-05T17:31:00Z'));
        $event1->method('getData')->willReturn(['key' => 'value']);
        $event1->method('getExtensions')->willReturn(['comacme' => 'foo']);

        /** @var CloudEventInterface|Stub $event */
        $event2 = $this->createStub(CloudEventInterface::class);
        $event2->method('getSpecVersion')->willReturn('0.3');

        $formatter = JsonSerializer::create();

        $this->expectException(UnsupportedSpecVersionException::class);

        $formatter->serializeBatch([$event1, $event2]);
    }

    public function testSerializeBinary(): void
    {
        /** @var CloudEventInterfaceV1|Stub $event */
        $event = $this->createStub(CloudEventInterfaceV1::class);
        $event->method('getSpecVersion')->willReturn('1.0');
        $event->method('getId')->willReturn('1234-1234-1234');
        $event->method('getSource')->willReturn('/var/data');
        $event->method('getType')->willReturn('com.example.someevent');
        $event->method('getDataContentType')->willReturn('application/json');
        $event->method('getDataSchema')->willReturn('com.example/schema');
        $event->method('getSubject')->willReturn('larger-context');
        $event->method('getTime')->willReturn(new DateTimeImmutable('2018-04-05T17:31:00Z'));
        $event->method('getData')->willReturn(['key' => 'value']);
        $event->method('getExtensions')->willReturn(['comacme' => 'foo', 'comacmen' => 123, 'comacmet' => true, 'comacmef' => false]);

        $formatter = JsonSerializer::create();

        self::assertSame(
            [
                'data' => '{"key":"value"}',
                'contentType' => 'application/json',
                'attributes' => [
                    'specversion' => '1.0',
                    'id' => '1234-1234-1234',
                    'source' => '%2Fvar%2Fdata',
                    'type' => 'com.example.someevent',
                    'dataschema' => 'com.example%2Fschema',
                    'subject' => 'larger-context',
                    'time' => '2018-04-05T17%3A31%3A00Z',
                    'comacme' => 'foo',
                    'comacmen' => '123',
                    'comacmet' => 'true',
                    'comacmef' => 'false',
                ],
            ],
            $formatter->serializeBinary($event)
        );
    }

    public function testSerializeBinaryUnsupportedSpecVersion(): void
    {
        /** @var CloudEventInterface|Stub $event */
        $event = $this->createStub(CloudEventInterface::class);
        $event->method('getSpecVersion')->willReturn('0.3');

        $formatter = JsonSerializer::create();

        $this->expectException(UnsupportedSpecVersionException::class);

        $formatter->serializeBinary($event);
    }
}
