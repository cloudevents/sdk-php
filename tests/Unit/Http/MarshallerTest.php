<?php

declare(strict_types=1);

namespace Tests\Unit\Http;

use CloudEvents\V1\CloudEventInterface;
use CloudEvents\Http\Marshaller;
use CloudEvents\Http\MarshallerInterface;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Message;

class MarshallerTest extends TestCase
{
    public function testInstantiate(): void
    {
        self::assertInstanceOf(
            MarshallerInterface::class,
            Marshaller::createJsonMarshaller()
        );
    }

    public function testMarshalStructuredRequest(): void
    {
        /** @var CloudEventInterfaceV1|Stub $event */
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

        $request = Marshaller::createJsonMarshaller()
            ->marshalStructuredRequest($event, 'GET', 'https://example.com/endpoint');

        self::assertSame(
            "GET /endpoint HTTP/1.1\r\nHost: example.com\r\nContent-Type: application/cloudevents+json\r\nContent-Length: 266\r\n\r\n{\"specversion\":\"1.0\",\"id\":\"1234-1234-1234\",\"source\":\"\/var\/data\",\"type\":\"com.example.someevent\",\"datacontenttype\":\"application\/json\",\"dataschema\":\"com.example\/schema\",\"subject\":\"larger-context\",\"time\":\"2018-04-05T17:31:00Z\",\"comacme\":\"foo\",\"data\":{\"key\":\"value\"}}",
            Message::toString($request)
        );
    }

    public function testMarshalBinaryRequest(): void
    {
        /** @var CloudEventInterfaceV1|Stub $event */
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

        $request = Marshaller::createJsonMarshaller()
            ->marshalBinaryRequest($event, 'GET', 'https://example.com/endpoint');

        self::assertSame(
            "GET /endpoint HTTP/1.1\r\nHost: example.com\r\nContent-Type: application/json\r\nContent-Length: 15\r\nce-specversion: 1.0\r\nce-id: 1234-1234-1234\r\nce-source: %2Fvar%2Fdata\r\nce-type: com.example.someevent\r\nce-dataschema: com.example%2Fschema\r\nce-subject: larger-context\r\nce-time: 2018-04-05T17%3A31%3A00Z\r\nce-comacme: foo\r\n\r\n{\"key\":\"value\"}",
            Message::toString($request)
        );
    }

    public function testMarshalBatchRequest(): void
    {
        /** @var CloudEventInterfaceV1|Stub $event */
        $event1 = $this->createStub(CloudEventInterface::class);
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

        /** @var CloudEventInterfaceV1|Stub $event */
        $event2 = $this->createStub(CloudEventInterface::class);
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

        $request = Marshaller::createJsonMarshaller()
            ->marshalBatchRequest([$event1, $event2], 'GET', 'https://example.com/endpoint');

        self::assertSame(
            "GET /endpoint HTTP/1.1\r\nHost: example.com\r\nContent-Type: application/cloudevents-batch+json\r\nContent-Length: 535\r\n\r\n[{\"specversion\":\"1.0\",\"id\":\"1234-1234-1234\",\"source\":\"\/var\/data\",\"type\":\"com.example.someevent\",\"datacontenttype\":\"application\/json\",\"dataschema\":\"com.example\/schema\",\"subject\":\"larger-context\",\"time\":\"2018-04-05T17:31:00Z\",\"comacme\":\"foo\",\"data\":{\"key\":\"value\"}},{\"specversion\":\"1.0\",\"id\":\"1234-1234-2222\",\"source\":\"\/var\/data\",\"type\":\"com.example.someevent\",\"datacontenttype\":\"application\/json\",\"dataschema\":\"com.example\/schema\",\"subject\":\"larger-context\",\"time\":\"2018-04-05T17:31:00Z\",\"comacme\":\"foo\",\"data\":{\"key\":\"value\"}}]",
            Message::toString($request)
        );
    }

    public function testMarshalStructuredResponse(): void
    {
        /** @var CloudEventInterfaceV1|Stub $event */
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

        $response = Marshaller::createJsonMarshaller()
            ->marshalStructuredResponse($event);

        self::assertSame(
            "HTTP/1.1 200 OK\r\nContent-Type: application/cloudevents+json\r\nContent-Length: 266\r\n\r\n{\"specversion\":\"1.0\",\"id\":\"1234-1234-1234\",\"source\":\"\/var\/data\",\"type\":\"com.example.someevent\",\"datacontenttype\":\"application\/json\",\"dataschema\":\"com.example\/schema\",\"subject\":\"larger-context\",\"time\":\"2018-04-05T17:31:00Z\",\"comacme\":\"foo\",\"data\":{\"key\":\"value\"}}",
            Message::toString($response)
        );
    }

    public function testMarshalBinaryResponse(): void
    {
        /** @var CloudEventInterfaceV1|Stub $event */
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

        $response = Marshaller::createJsonMarshaller()
            ->marshalBinaryResponse($event);

        self::assertSame(
            "HTTP/1.1 200 OK\r\nContent-Type: application/json\r\nContent-Length: 15\r\nce-specversion: 1.0\r\nce-id: 1234-1234-1234\r\nce-source: %2Fvar%2Fdata\r\nce-type: com.example.someevent\r\nce-dataschema: com.example%2Fschema\r\nce-subject: larger-context\r\nce-time: 2018-04-05T17%3A31%3A00Z\r\nce-comacme: foo\r\n\r\n{\"key\":\"value\"}",
            Message::toString($response)
        );
    }

    public function testMarshalBatchResponse(): void
    {
        /** @var CloudEventInterfaceV1|Stub $event */
        $event1 = $this->createStub(CloudEventInterface::class);
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

        /** @var CloudEventInterfaceV1|Stub $event */
        $event2 = $this->createStub(CloudEventInterface::class);
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

        $response = Marshaller::createJsonMarshaller()
            ->marshalBatchResponse([$event1, $event2]);

        self::assertSame(
            "HTTP/1.1 200 OK\r\nContent-Type: application/cloudevents-batch+json\r\nContent-Length: 535\r\n\r\n[{\"specversion\":\"1.0\",\"id\":\"1234-1234-1234\",\"source\":\"\/var\/data\",\"type\":\"com.example.someevent\",\"datacontenttype\":\"application\/json\",\"dataschema\":\"com.example\/schema\",\"subject\":\"larger-context\",\"time\":\"2018-04-05T17:31:00Z\",\"comacme\":\"foo\",\"data\":{\"key\":\"value\"}},{\"specversion\":\"1.0\",\"id\":\"1234-1234-2222\",\"source\":\"\/var\/data\",\"type\":\"com.example.someevent\",\"datacontenttype\":\"application\/json\",\"dataschema\":\"com.example\/schema\",\"subject\":\"larger-context\",\"time\":\"2018-04-05T17:31:00Z\",\"comacme\":\"foo\",\"data\":{\"key\":\"value\"}}]",
            Message::toString($response)
        );
    }
}
