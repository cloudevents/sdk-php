<?php

declare(strict_types=1);

namespace Tests\Unit\V1;

use CloudEvents\CloudEventInterface;
use CloudEvents\V1\CloudEventImmutable;
use CloudEvents\V1\CloudEventInterface as CloudEventInterfaceV1;
use PHPUnit\Framework\TestCase;
use TypeError;
use ValueError;

class CloudEventImmutableTest extends TestCase
{
    public function testInstantiate(): void
    {
        $event = new CloudEventImmutable('foo', 'bar', 'baz');
        self::assertInstanceOf(CloudEventImmutable::class, $event);
        self::assertInstanceOf(CloudEventInterface::class, $event);
        self::assertInstanceOf(CloudEventInterfaceV1::class, $event);
    }

    public function testGetSpecVersion(): void
    {
        self::assertEquals('1.0', $this->getEvent()->getSpecVersion());
    }

    public function testGetSetId(): void
    {
        $event = $this->getEvent();
        self::assertEquals('A234-1234-1234', $event->getId());
        $event = $event->withId('new-id');
        self::assertEquals('new-id', $event->getId());
    }

    public function testGetSetSource(): void
    {
        $event = $this->getEvent();
        self::assertEquals('https://github.com/cloudevents/spec/pull', $event->getSource());
        $event = $event->withSource('new-source');
        self::assertEquals('new-source', $event->getSource());
    }

    public function testGetSetType(): void
    {
        $event = $this->getEvent();
        self::assertEquals('com.github.pull_request.opened', $event->getType());
        $event = $event->withType('new-type');
        self::assertEquals('new-type', $event->getType());
    }

    public function testGetSetDataContentType(): void
    {
        $event = $this->getEvent();
        self::assertEquals('text/xml', $event->getDataContentType());
        $event = $event->withDataContentType('application/json');
        self::assertEquals('application/json', $event->getDataContentType());
    }

    public function testGetSetDataSchema(): void
    {
        $event = $this->getEvent();
        self::assertEquals(null, $event->getDataSchema());
        $event = $event->withDataSchema('new-schema');
        self::assertEquals('new-schema', $event->getDataSchema());
    }

    public function testGetSetSubject(): void
    {
        $event = $this->getEvent();
        self::assertEquals('123', $event->getSubject());
        $event = $event->withSubject('new-subject');
        self::assertEquals('new-subject', $event->getSubject());
    }

    public function testGetSetTime(): void
    {
        $event = $this->getEvent();
        self::assertEquals(new \DateTimeImmutable('2018-04-05T17:31:00Z'), $event->getTime());
        $event = $event->withTime(new \DateTimeImmutable('2021-01-19T17:31:00Z'));
        self::assertEquals(new \DateTimeImmutable('2021-01-19T17:31:00Z'), $event->getTime());
    }

    public function testGetSetData(): void
    {
        $event = $this->getEvent();
        self::assertEquals('<much wow=\"xml\"/>', $event->getData());
        $event = $event->withData('{"key": "value"}');
        self::assertEquals('{"key": "value"}', $event->getData());
    }

    public function testCannotSetEmptyExtensionValueType(): void
    {
        $event = $this->getEvent();

        $this->expectException(ValueError::class);

        $event->withExtension('', '1.1');
    }

    public function testCannotSetInvalidExtensionAttribute(): void
    {
        $event = $this->getEvent();

        $this->expectException(ValueError::class);

        $event->withExtension('comBAD', '1.1');
    }

    public function testCannotSetReservedExtensionAttribute(): void
    {
        $event = $this->getEvent();

        $this->expectException(ValueError::class);

        $event->withExtension('specversion', '1.1');
    }

    public function testCannotSetInvalidExtensionValueType(): void
    {
        $event = $this->getEvent();

        $this->expectException(TypeError::class);

        $event->withExtension('comacme', 1.1);
    }

    public function testCanSetAndUnsetExtensions(): void
    {
        $event = $this->getEvent();
        self::assertEquals([], $event->getExtensions());
        self::assertNull($event->getExtension('comacme'));
        self::assertNull($event->getExtension('comacme2'));
        $event = $event->withExtension('comacme', 'foo');
        self::assertEquals('foo', $event->getExtension('comacme'));
        self::assertEquals(['comacme' => 'foo'], $event->getExtensions());
        $event = $event->withExtension('comacme2', 123);
        self::assertEquals(123, $event->getExtension('comacme2'));
        self::assertEquals(['comacme' => 'foo', 'comacme2' => 123], $event->getExtensions());
        $event = $event->withExtension('comacme', null);
        self::assertNull($event->getExtension('comacme'));
        self::assertEquals(['comacme2' => 123], $event->getExtensions());
        $event = $event->withExtensions(['comacme' => 12345, 'comacme2' => null]);
        self::assertEquals(12345, $event->getExtension('comacme'));
        self::assertNull($event->getExtension('comacme2'));
        self::assertEquals(['comacme' => 12345], $event->getExtensions());
    }

    public function testCanCreateFromInterface(): void
    {
        $original = $this->getEvent();
        $event = CloudEventImmutable::createFromInterface($original);
        self::assertInstanceOf(CloudEventImmutable::class, $original);
        self::assertInstanceOf(CloudEventInterface::class, $original);
        self::assertInstanceOf(CloudEventInterfaceV1::class, $original);
        self::assertSame($original->getId(), $event->getId());
        self::assertSame($original->getSource(), $event->getSource());
        self::assertSame($original->getType(), $event->getType());
        self::assertSame($original->getData(), $event->getData());
        self::assertSame($original->getDataContentType(), $event->getDataContentType());
        self::assertSame($original->getDataSchema(), $event->getDataSchema());
        self::assertSame($original->getSubject(), $event->getSubject());
        self::assertSame($original->getTime(), $event->getTime());
        self::assertSame($original->getExtensions(), $event->getExtensions());
    }

    private function getEvent(): CloudEventImmutable
    {
        return new CloudEventImmutable(
            'A234-1234-1234',
            'https://github.com/cloudevents/spec/pull',
            'com.github.pull_request.opened',
            '<much wow=\"xml\"/>',
            'text/xml',
            null,
            '123',
            new \DateTimeImmutable('2018-04-05T17:31:00Z')
        );
    }
}
