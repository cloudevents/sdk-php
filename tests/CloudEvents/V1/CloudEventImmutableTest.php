<?php

namespace CloudEvents\Tests\CloudEvents\V1;

use CloudEvents\V1\CloudEventImmutable;
use CloudEvents\V1\CloudEventInterface;
use PHPUnit\Framework\TestCase;
use TypeError;
use ValueError;

/**
 * @coversDefaultClass \CloudEvents\V1\CloudEventImmutable
 * @coversDefaultClass \CloudEvents\V1\CloudEventTrait
 */
class CloudEventImmutableTest extends TestCase
{
    /**
     * @covers ::getSpecVersion
     */
    public function testGetSpecVersion(): void
    {
        $this->assertEquals('1.0', $this->getEvent()->getSpecVersion());
    }

    /**
     * @covers ::getId
     * @covers ::withId
     */
    public function testGetSetId(): void
    {
        $event = $this->getEvent();
        $this->assertEquals('A234-1234-1234', $event->getId());
        $event = $event->withId('new-id');
        $this->assertEquals('new-id', $event->getId());
    }

    /**
     * @covers ::getSource
     * @covers ::withSource
     */
    public function testGetSetSource(): void
    {
        $event = $this->getEvent();
        $this->assertEquals('https://github.com/cloudevents/spec/pull', $event->getSource());
        $event = $event->withSource('new-source');
        $this->assertEquals('new-source', $event->getSource());
    }

    /**
     * @covers ::getType
     * @covers ::withType
     */
    public function testGetSetType(): void
    {
        $event = $this->getEvent();
        $this->assertEquals('com.github.pull_request.opened', $event->getType());
        $event = $event->withType('new-type');
        $this->assertEquals('new-type', $event->getType());
    }

    /**
     * @covers ::getDataContentType
     * @covers ::withDataContentType
     */
    public function testGetSetDataContentType(): void
    {
        $event = $this->getEvent();
        $this->assertEquals('text/xml', $event->getDataContentType());
        $event = $event->withDataContentType('application/json');
        $this->assertEquals('application/json', $event->getDataContentType());
    }

    /**
     * @covers ::getDataSchema
     * @covers ::withDataSchema
     */
    public function testGetSetDataSchema(): void
    {
        $event = $this->getEvent();
        $this->assertEquals(null, $event->getDataSchema());
        $event = $event->withDataSchema('new-schema');
        $this->assertEquals('new-schema', $event->getDataSchema());
    }

    /**
     * @covers ::getSubject
     * @covers ::withSubject
     */
    public function testGetSetSubject(): void
    {
        $event = $this->getEvent();
        $this->assertEquals('123', $event->getSubject());
        $event = $event->withSubject('new-subject');
        $this->assertEquals('new-subject', $event->getSubject());
    }

    /**
     * @covers ::getTime
     * @covers ::withTime
     */
    public function testGetSetTime(): void
    {
        $event = $this->getEvent();
        $this->assertEquals(new \DateTimeImmutable('2018-04-05T17:31:00Z'), $event->getTime());
        $event = $event->withTime(new \DateTimeImmutable('2021-01-19T17:31:00Z'));
        $this->assertEquals(new \DateTimeImmutable('2021-01-19T17:31:00Z'), $event->getTime());
    }

    /**
     * @covers ::getData
     * @covers ::withData
     */
    public function testGetSetData(): void
    {
        $event = $this->getEvent();
        $this->assertEquals('<much wow=\"xml\"/>', $event->getData());
        $event = $event->withData('{"key": "value"}');
        $this->assertEquals('{"key": "value"}', $event->getData());
    }

    /**
     * @covers ::withExtension
     */
    public function testCannotSetEmptyExtensionValueType(): void
    {
        $event = $this->getEvent();

        $this->expectException(ValueError::class);

        $event->withExtension('', '1.1');
    }

    /**
     * @covers ::withExtension
     */
    public function testCannotSetInvalidExtensionAttribute(): void
    {
        $event = $this->getEvent();

        $this->expectException(ValueError::class);

        $event->withExtension('comBAD', '1.1');
    }

    /**
     * @covers ::withExtension
     */
    public function testCannotSetReservedExtensionAttribute(): void
    {
        $event = $this->getEvent();

        $this->expectException(ValueError::class);

        $event->withExtension('specversion', '1.1');
    }

    /**
     * @covers ::withExtension
     */
    public function testCannotSetInvalidExtensionValueType(): void
    {
        $event = $this->getEvent();

        $this->expectException(TypeError::class);

        $event->withExtension('comacme', 1.1);
    }

    /**
     * @covers ::withExtension
     * @covers ::getExtension
     * @covers ::getExtensions
     */
    public function testCanSetAndUnsetExtensions(): void
    {
        $event = $this->getEvent();
        $this->assertEquals([], $event->getExtensions());
        $this->assertNull($event->getExtension('comacme'));
        $this->assertNull($event->getExtension('comacme2'));
        $event = $event->withExtension('comacme', 'foo');
        $this->assertEquals('foo', $event->getExtension('comacme'));
        $this->assertEquals(['comacme' => 'foo'], $event->getExtensions());
        $event = $event->withExtension('comacme2', 123);
        $this->assertEquals(123, $event->getExtension('comacme2'));
        $this->assertEquals(['comacme' => 'foo', 'comacme2' => 123], $event->getExtensions());
        $event = $event->withExtension('comacme', null);
        $this->assertNull($event->getExtension('comacme'));
        $this->assertEquals(['comacme2' => 123], $event->getExtensions());
    }

    /**
     * @covers ::createFromInterface
     */
    public function testCanCreateFromInterface(): void
    {
        $original = $this->getEvent();
        $event = CloudEventImmutable::createFromInterface($original);
        $this->assertInstanceOf(CloudEventImmutable::class, $original);
        $this->assertInstanceOf(CloudEventInterface::class, $original);
        $this->assertSame($original->getId(), $event->getId());
        $this->assertSame($original->getSource(), $event->getSource());
        $this->assertSame($original->getType(), $event->getType());
        $this->assertSame($original->getData(), $event->getData());
        $this->assertSame($original->getDataContentType(), $event->getDataContentType());
        $this->assertSame($original->getDataSchema(), $event->getDataSchema());
        $this->assertSame($original->getSubject(), $event->getSubject());
        $this->assertSame($original->getTime(), $event->getTime());
        $this->assertSame($original->getExtensions(), $event->getExtensions());
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
