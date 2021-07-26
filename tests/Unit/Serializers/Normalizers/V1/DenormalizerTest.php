<?php

declare(strict_types=1);

namespace Tests\Unit\Serializers\Normalizers\V1;

use CloudEvents\Serializers\Normalizers\V1\Denormalizer;
use CloudEvents\Serializers\Normalizers\V1\DenormalizerInterface;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use CloudEvents\Exceptions\MissingAttributeException;

class DenormalizerTest extends TestCase
{
    public function testInstantiate(): void
    {
        self::assertInstanceOf(DenormalizerInterface::class, new Denormalizer());
    }

    public function testDenormalize(): void
    {
        $payload = [
            'specversion' => '1.0',
            'id' => '1234-1234-1234',
            'source' => '/var/data',
            'type' => 'com.example.someevent',
            'datacontenttype' => 'application/json',
            'dataschema' => 'com.example/schema',
            'subject' => 'larger-context',
            'time' => '2018-04-05T17:31:00Z',
            'data' => [
                'key' => 'value',
            ],
            'comacme' => 'foo',
        ];

        $formatter = new Denormalizer();

        $event = $formatter->denormalize($payload);

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

    public function testDenormalizeMissingId(): void
    {
        $payload = [
            'specversion' => '1.0',
            'source' => '/var/data',
            'type' => 'com.example.someevent',
            'datacontenttype' => 'application/json',
            'dataschema' => 'com.example/schema',
            'subject' => 'larger-context',
            'time' => '2018-04-05T17:31:00Z',
            'data' => [
                'key' => 'value',
            ],
            'comacme' => 'foo',
        ];

        $formatter = new Denormalizer();

        $this->expectException(MissingAttributeException::class);

        $formatter->denormalize($payload);
    }
}
