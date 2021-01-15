<?php

declare(strict_types=1);

namespace CloudEvents\Tests;

use CloudEvents\Event;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    /**
     * @covers \CloudEvents\Event
     */
    public function testDefaultSpecVersion(): void
    {
        $this->assertEquals(
            Event::VERSION_1_0,
            (new Event())->getSpecVersion()
        );
    }

    public function validAttributesProvider(): array
    {
        return [
            'id'          => ['getId', 'setId', bin2hex(random_bytes(8))],
            'source-uri'  => ['getSource', 'setSource', 'https://github.com/cloudevents/php-sdk'],
            'source-urn'  => ['getSource', 'setSource', 'urn:ksuid:1n4IVJiQ3NIGrJBoS2l1wpOxtil'],
            'source-app'  => ['getSource', 'setSource', '/cloudevents/php/test/123'],
            'specversion' => ['getSpecVersion', 'setSpecVersion', '1.0'],
            'type'        => ['getType', 'setType', 'com.example.php.test.v1'],
        ];
    }

    /**
     * @covers \CloudEvents\Event
     * @dataProvider validAttributesProvider
     */
    public function testValidAttributeSetAndGet(
        string $getter,
        string $setter,
        $value
    ): void {
        $event = (new Event())->{$setter}($value);

        $this->assertEquals($value, $event->{$getter}());
    }
}
