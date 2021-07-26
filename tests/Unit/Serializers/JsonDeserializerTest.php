<?php

declare(strict_types=1);

namespace Tests\Unit\Serializers;

use CloudEvents\Serializers\JsonDeserializer;
use CloudEvents\Serializers\DeserializerInterface;
use CloudEvents\Serializers\Normalizers\V1\DenormalizerInterface;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use CloudEvents\Exceptions\InvalidAttributeException;
use CloudEvents\Exceptions\InvalidPayloadSyntaxException;
use CloudEvents\Exceptions\UnsupportedSpecVersionException;

class JsonDeserializerTest extends TestCase
{
    public function testInstantiate(): void
    {
        self::assertInstanceOf(
            DeserializerInterface::class,
            new JsonDeserializer($this->createStub(DenormalizerInterface::class))
        );

        self::assertInstanceOf(
            DeserializerInterface::class,
            JsonDeserializer::create()
        );
    }

    public function testDeserializeStructured(): void
    {
        $formatter = JsonDeserializer::create();

        $event = $formatter->deserializeStructured(
            '{"specversion":"1.0","id":"1234-1234-1234","source":"\/var\/data","type":"com.example.someevent","datacontenttype":"application\/json","dataschema":"com.example\/schema","subject":"larger-context","time":"2018-04-05T17:31:00Z","data":{"key":"value"},"comacme":"foo"}'
        );

        self::assertEquals('1.0', $event->getSpecVersion());
        self::assertEquals('1234-1234-1234', $event->getId());
        self::assertEquals('/var/data', $event->getSource());
        self::assertEquals('com.example.someevent', $event->getType());
        self::assertEquals('application/json', $event->getDataContentType());
        self::assertEquals('com.example/schema', $event->getDataSchema());
        self::assertEquals('larger-context', $event->getSubject());
        self::assertEquals(new DateTimeImmutable('2018-04-05T17:31:00Z'), $event->getTime());
        self::assertEquals(['key' => 'value'], $event->getData());
        self::assertEquals(['comacme' => 'foo'], $event->getExtensions());
    }

    public function testDeserializeStructuredUnsupportedSpecVersion(): void
    {
        $formatter = JsonDeserializer::create();

        $this->expectException(UnsupportedSpecVersionException::class);

        $formatter->deserializeStructured(
            '{"specversion":"0.3","id":"1234-1234-1234","source":"\/var\/data","type":"com.example.someevent","datacontenttype":"application\/json","dataschema":"com.example\/schema","subject":"larger-context","time":"2018-04-05T17:31:00Z","data":{"key":"value"},"comacme":"foo"}'
        );
    }

    public function testDeserializeStructuredInvalidSyntax1(): void
    {
        $formatter = JsonDeserializer::create();

        $this->expectException(InvalidPayloadSyntaxException::class);

        $formatter->deserializeStructured(
            '{"specversion":"1.0","id":"1234-1'
        );
    }

    public function testDeserializeStructuredInvalidSyntax2(): void
    {
        $formatter = JsonDeserializer::create();

        $this->expectException(InvalidPayloadSyntaxException::class);

        $formatter->deserializeStructured(
            'true'
        );
    }

    public function testDeserializeStructuredInvalidId(): void
    {
        $formatter = JsonDeserializer::create();

        $this->expectException(InvalidAttributeException::class);

        $formatter->deserializeStructured(
            '{"specversion":"1.0","id":123,"source":"\/var\/data","type":"com.example.someevent","datacontenttype":"application\/json","dataschema":"com.example\/schema","subject":"larger-context","time":"2018-04-05T17:31:00Z","data":{"key":"value"},"comacme":"foo"}'
        );
    }

    public function testDeserializeBatch(): void
    {
        $formatter = JsonDeserializer::create();

        $events = $formatter->deserializeBatch(
            '[{"specversion":"1.0","id":"1234-1234-1234","source":"\/var\/data","type":"com.example.someevent","datacontenttype":"application\/json","dataschema":"com.example\/schema","subject":"larger-context","time":"2018-04-05T17:31:00Z","data":{"key":"value"},"comacme":"foo"},{"specversion":"1.0","id":"1234-1234-2222","source":"\/var\/data","type":"com.example.someevent","datacontenttype":"application\/json","dataschema":"com.example\/schema","subject":"larger-context","time":"2018-04-05T17:31:00Z","data":{"key":"value"},"comacme":"foo"}]'
        );

        self::assertCount(2, $events);

        self::assertEquals('1.0', $events[0]->getSpecVersion());
        self::assertEquals('1234-1234-1234', $events[0]->getId());
        self::assertEquals('/var/data', $events[0]->getSource());
        self::assertEquals('com.example.someevent', $events[0]->getType());
        self::assertEquals('application/json', $events[0]->getDataContentType());
        self::assertEquals('com.example/schema', $events[0]->getDataSchema());
        self::assertEquals('larger-context', $events[0]->getSubject());
        self::assertEquals(new DateTimeImmutable('2018-04-05T17:31:00Z'), $events[0]->getTime());
        self::assertEquals(['key' => 'value'], $events[0]->getData());
        self::assertEquals(['comacme' => 'foo'], $events[0]->getExtensions());

        self::assertEquals('1.0', $events[1]->getSpecVersion());
        self::assertEquals('1234-1234-2222', $events[1]->getId());
        self::assertEquals('/var/data', $events[1]->getSource());
        self::assertEquals('com.example.someevent', $events[1]->getType());
        self::assertEquals('application/json', $events[1]->getDataContentType());
        self::assertEquals('com.example/schema', $events[1]->getDataSchema());
        self::assertEquals('larger-context', $events[1]->getSubject());
        self::assertEquals(new DateTimeImmutable('2018-04-05T17:31:00Z'), $events[1]->getTime());
        self::assertEquals(['key' => 'value'], $events[1]->getData());
        self::assertEquals(['comacme' => 'foo'], $events[1]->getExtensions());
    }

    public function testDeserializeBatchUnsupportedSpecVersion(): void
    {
        $formatter = JsonDeserializer::create();

        $this->expectException(UnsupportedSpecVersionException::class);

        $formatter->deserializeBatch(
            '[{"specversion":"1.0","id":"1234-1234-1234","source":"\/var\/data","type":"com.example.someevent","datacontenttype":"application\/json","dataschema":"com.example\/schema","subject":"larger-context","time":"2018-04-05T17:31:00Z","data":{"key":"value"},"comacme":"foo"},{"specversion":"0.3","id":"1234-1234-2222","source":"\/var\/data","type":"com.example.someevent","datacontenttype":"application\/json","dataschema":"com.example\/schema","subject":"larger-context","time":"2018-04-05T17:31:00Z","data":{"key":"value"},"comacme":"foo"}]'
        );
    }

    public function testDeserializeBatchInvalidSyntax1(): void
    {
        $formatter = JsonDeserializer::create();

        $this->expectException(InvalidPayloadSyntaxException::class);

        $formatter->deserializeBatch(
            '[{"specversion":"1.0","id":"1234-1'
        );
    }

    public function testDeserializeBatchInvalidSyntax2(): void
    {
        $formatter = JsonDeserializer::create();

        $this->expectException(InvalidPayloadSyntaxException::class);

        $formatter->deserializeBatch(
            'true'
        );
    }

    public function testDeserializeBatchInvalidSyntax3(): void
    {
        $formatter = JsonDeserializer::create();

        $this->expectException(InvalidPayloadSyntaxException::class);

        $formatter->deserializeBatch(
            '[true]'
        );
    }

    public function testDeserializeBinary(): void
    {
        $formatter = JsonDeserializer::create();

        $event = $formatter->deserializeBinary(
            '{"key":"value"}',
            'application/json',
            [
                'specversion' => '1.0',
                'id' => '1234-1234-1234',
                'source' => '%2Fvar%2Fdata',
                'type' => 'com.example.someevent',
                'dataschema' => 'com.example%2Fschema',
                'subject' => 'larger-context',
                'time' => '2018-04-05T17%3A31%3A00Z',
                'comacme' => 'foo',
            ]
        );

        self::assertEquals('1.0', $event->getSpecVersion());
        self::assertEquals('1234-1234-1234', $event->getId());
        self::assertEquals('/var/data', $event->getSource());
        self::assertEquals('com.example.someevent', $event->getType());
        self::assertEquals('application/json', $event->getDataContentType());
        self::assertEquals('com.example/schema', $event->getDataSchema());
        self::assertEquals('larger-context', $event->getSubject());
        self::assertEquals(new DateTimeImmutable('2018-04-05T17:31:00Z'), $event->getTime());
        self::assertEquals(['key' => 'value'], $event->getData());
        self::assertEquals(['comacme' => 'foo'], $event->getExtensions());
    }

    public function testDeserializeBinaryUnsupportedSpecVersion(): void
    {
        $formatter = JsonDeserializer::create();

        $this->expectException(UnsupportedSpecVersionException::class);

        $formatter->deserializeBinary(
            '{"key":"value"}',
            'application/json',
            [
                'specversion' => '0.3',
                'id' => '1234-1234-1234',
                'source' => '%2Fvar%2Fdata',
                'type' => 'com.example.someevent',
                'dataschema' => 'com.example%2Fschema',
                'subject' => 'larger-context',
                'time' => '2018-04-05T17%3A31%3A00Z',
                'comacme' => 'foo',
            ]
        );
    }

    public function testDeserializeBinaryInvalidSyntax1(): void
    {
        $formatter = JsonDeserializer::create();

        $this->expectException(InvalidPayloadSyntaxException::class);

        $formatter->deserializeBinary(
            '{"key":"val',
            'application/json',
            [
                'specversion' => '0.3',
                'id' => '1234-1234-1234',
                'source' => '%2Fvar%2Fdata',
                'type' => 'com.example.someevent',
                'dataschema' => 'com.example%2Fschema',
                'subject' => 'larger-context',
                'time' => '2018-04-05T17%3A31%3A00Z',
                'comacme' => 'foo',
            ]
        );
    }
}
