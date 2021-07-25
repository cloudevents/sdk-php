<?php

declare(strict_types=1);

namespace Tests\Unit\Serializers\Normalizers\V1;

use CloudEvents\Serializers\Normalizers\V1\Normalizer;
use CloudEvents\Serializers\Normalizers\V1\NormalizerInterface;
use CloudEvents\V1\CloudEventInterface;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

class NormalizerTest extends TestCase
{
    public function testInstantiate(): void
    {
        self::assertInstanceOf(NormalizerInterface::class, new Normalizer());
    }

    public function testNormalizer(): void
    {
        /** @var CloudEventInterface|Stub $event */
        $event = $this->createStub(CloudEventInterface::class);
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

        $formatter = new Normalizer();

        self::assertSame(
            [
                'specversion' => '1.0',
                'id' => '1234-1234-1234',
                'source' => '/var/data',
                'type' => 'com.example.someevent',
                'datacontenttype' => 'application/json',
                'dataschema' => 'com.example/schema',
                'subject' => 'larger-context',
                'time' => '2018-04-05T17:31:00Z',
                'comacme' => 'foo',
                'data' => [
                    'key' => 'value',
                ],
            ],
            $formatter->normalize($event, false)
        );
    }

    public function testNormalizerWithUnsetAttributes(): void
    {
        /** @var CloudEventInterface|Stub $event */
        $event = $this->createStub(CloudEventInterface::class);
        $event->method('getSpecVersion')->willReturn('1.0');
        $event->method('getId')->willReturn('1234-1234-1234');
        $event->method('getSource')->willReturn('/var/data');
        $event->method('getType')->willReturn('com.example.someevent');
        $event->method('getSubject')->willReturn('larger-context');
        $event->method('getTime')->willReturn(new DateTimeImmutable('2018-04-05T17:31:00Z'));
        $event->method('getExtensions')->willReturn([]);

        $formatter = new Normalizer();

        self::assertSame(
            [
                'specversion' => '1.0',
                'id' => '1234-1234-1234',
                'source' => '/var/data',
                'type' => 'com.example.someevent',
                'subject' => 'larger-context',
                'time' => '2018-04-05T17:31:00Z',
            ],
            $formatter->normalize($event, false)
        );
    }
}
