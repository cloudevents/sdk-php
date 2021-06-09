<?php

namespace CloudEvents\Tests\CloudEvents\V1;

use CloudEvents\V1\CloudEvent;
use PHPUnit\Framework\TestCase;
use TypeError;
use ValueError;

/**
 * @coversDefaultClass \CloudEvents\V1\CloudEvent
 */
class CloudEventTest extends TestCase
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
     * @covers ::setId
     */
    public function testGetSetId(): void
    {
        $event = $this->getEvent();
        $this->assertEquals('A234-1234-1234', $event->getId());
        $event = $event->setId('new-id');
        $this->assertEquals('new-id', $event->getId());
    }

    /**
     * @covers ::getSource
     * @covers ::setSource
     */
    public function testGetSetSource(): void
    {
        $event = $this->getEvent();
        $this->assertEquals('https://github.com/cloudevents/spec/pull', $event->getSource());
        $event = $event->setSource('new-source');
        $this->assertEquals('new-source', $event->getSource());
    }

    /**
     * @covers ::getType
     * @covers ::setType
     */
    public function testGetSetType(): void
    {
        $event = $this->getEvent();
        $this->assertEquals('com.github.pull_request.opened', $event->getType());
        $event = $event->setType('new-type');
        $this->assertEquals('new-type', $event->getType());
    }

    /**
     * @covers ::getDataContentType
     * @covers ::setDataContentType
     */
    public function testGetSetDataContentType(): void
    {
        $event = $this->getEvent();
        $this->assertEquals('text/xml', $event->getDataContentType());
        $event = $event->setDataContentType('application/json');
        $this->assertEquals('application/json', $event->getDataContentType());
    }

    /**
     * @covers ::getDataSchema
     * @covers ::setDataSchema
     */
    public function testGetSetDataSchema(): void
    {
        $event = $this->getEvent();
        $this->assertEquals(null, $event->getDataSchema());
        $event = $event->setDataSchema('new-schema');
        $this->assertEquals('new-schema', $event->getDataSchema());
    }

    /**
     * @covers ::getSubject
     * @covers ::setSubject
     */
    public function testGetSetSubject(): void
    {
        $event = $this->getEvent();
        $this->assertEquals('123', $event->getSubject());
        $event = $event->setSubject('new-subject');
        $this->assertEquals('new-subject', $event->getSubject());
    }

    /**
     * @covers ::getTime
     * @covers ::setTime
     */
    public function testGetSetTime(): void
    {
        $event = $this->getEvent();
        $this->assertEquals(new \DateTimeImmutable('2018-04-05T17:31:00Z'), $event->getTime());
        $event = $event->setTime(new \DateTimeImmutable('2021-01-19T17:31:00Z'));
        $this->assertEquals(new \DateTimeImmutable('2021-01-19T17:31:00Z'), $event->getTime());
    }

    /**
     * @covers ::getData
     * @covers ::setData
     */
    public function testGetSetData(): void
    {
        $event = $this->getEvent();
        $this->assertEquals('<much wow=\"xml\"/>', $event->getData());
        $event = $event->setData('{"key": "value"}');
        $this->assertEquals('{"key": "value"}', $event->getData());
    }

    /**
     * @covers ::setExtension
     */
    public function testCannotSetEmptyExtensionValueType(): void
    {
        $event = $this->getEvent();

        $this->expectException(ValueError::class);

        $event->setExtension('', '1.1');
    }

    /**
     * @covers ::setExtension
     */
    public function testCannotSetInvalidExtensionAttribute(): void
    {
        $event = $this->getEvent();

        $this->expectException(ValueError::class);

        $event->setExtension('comBAD', '1.1');
    }

    /**
     * @covers ::setExtension
     */
    public function testCannotSetReservedExtensionAttribute(): void
    {
        $event = $this->getEvent();

        $this->expectException(ValueError::class);

        $event->setExtension('specversion', '1.1');
    }

    /**
     * @covers ::setExtension
     */
    public function testCannotSetInvalidExtensionValueType(): void
    {
        $event = $this->getEvent();

        $this->expectException(TypeError::class);

        $event->setExtension('comacme', 1.1);
    }

    /**
     * @covers ::setExtension
     * @covers ::getExtensions
     */
    public function testCabSetAndUnsetExtensions(): void
    {
        $event = $this->getEvent();
        $this->assertEquals([], $event->getExtensions());
        $event->setExtension('comacme', 'foo');
        $this->assertEquals(['comacme' => 'foo'], $event->getExtensions());
        $event->setExtension('comacme2', 123);
        $this->assertEquals(['comacme' => 'foo', 'comacme2' => 123], $event->getExtensions());
        $event->setExtension('comacme', null);
        $this->assertEquals(['comacme2' => 123], $event->getExtensions());
    }

    private function getEvent(): CloudEvent
    {
        return new CloudEvent(
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
