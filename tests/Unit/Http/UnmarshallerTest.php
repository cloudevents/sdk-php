<?php

declare(strict_types=1);

namespace Tests\Unit\Http;

use CloudEvents\V1\CloudEventInterface;
use CloudEvents\Http\Unmarshaller;
use CloudEvents\Http\UnmarshallerInterface;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Message;
use CloudEvents\Exceptions\UnsupportedContentTypeException;

class UnmarshallerTest extends TestCase
{
    public function testInstantiate(): void
    {
        self::assertInstanceOf(
            UnmarshallerInterface::class,
            Unmarshaller::createJsonUnmarshaller()
        );
    }

    public function testUnmarshalStructuredRequest(): void
    {
        $events = Unmarshaller::createJsonUnmarshaller()->unmarshal(
            Message::parseRequest(
                "GET /endpoint HTTP/1.1\r\nHost: example.com\r\nContent-Type: application/cloudevents+json\r\nContent-Length: 266\r\n\r\n{\"specversion\":\"1.0\",\"id\":\"1234-1234-1234\",\"source\":\"\/var\/data\",\"type\":\"com.example.someevent\",\"datacontenttype\":\"application\/json\",\"dataschema\":\"com.example\/schema\",\"subject\":\"larger-context\",\"time\":\"2018-04-05T17:31:00Z\",\"comacme\":\"foo\",\"data\":{\"key\":\"value\"}}"
            )
        );

        self::assertCount(1, $events);

        self::assertInstanceOf(CloudEventInterface::class, $events[0]);
        self::assertSame('1.0', $events[0]->getSpecVersion());
        self::assertSame('1234-1234-1234', $events[0]->getId());
        self::assertSame('/var/data', $events[0]->getSource());
        self::assertSame('com.example.someevent', $events[0]->getType());
        self::assertSame('application/json', $events[0]->getDataContentType());
        self::assertSame('com.example/schema', $events[0]->getDataSchema());
        self::assertSame('larger-context', $events[0]->getSubject());
        self::assertSame(1522949460, $events[0]->getTime()->getTimestamp());
        self::assertSame(['key' => 'value'], $events[0]->getData());
        self::assertSame(['comacme' => 'foo'], $events[0]->getExtensions());
    }

    public function testUnmarshalStructuredRequestInvalidContentType(): void
    {
        $this->expectException(UnsupportedContentTypeException::class);

        Unmarshaller::createJsonUnmarshaller()->unmarshal(
            Message::parseRequest(
                "GET /endpoint HTTP/1.1\r\nHost: example.com\r\nContent-Type: application/cloudevents+xml\r\nContent-Length: 4\r\n\r\nDATA"
            )
        );
    }

    public function testUnmarshalBinaryRequest(): void
    {
        $events = Unmarshaller::createJsonUnmarshaller()->unmarshal(
            Message::parseRequest(
                "GET /endpoint HTTP/1.1\r\nHost: example.com\r\nContent-Type: application/json\r\nContent-Length: 15\r\nce-specversion: 1.0\r\nce-id: 1234-1234-1234\r\nce-source: %2Fvar%2Fdata\r\nce-type: com.example.someevent\r\nce-dataschema: com.example%2Fschema\r\nce-subject: larger-context\r\nce-time: 2018-04-05T17%3A31%3A00Z\r\nce-comacme: foo\r\n\r\n{\"key\":\"value\"}"
            )
        );

        self::assertCount(1, $events);

        self::assertInstanceOf(CloudEventInterface::class, $events[0]);
        self::assertSame('1.0', $events[0]->getSpecVersion());
        self::assertSame('1234-1234-1234', $events[0]->getId());
        self::assertSame('/var/data', $events[0]->getSource());
        self::assertSame('com.example.someevent', $events[0]->getType());
        self::assertSame('application/json', $events[0]->getDataContentType());
        self::assertSame('com.example/schema', $events[0]->getDataSchema());
        self::assertSame('larger-context', $events[0]->getSubject());
        self::assertSame(1522949460, $events[0]->getTime()->getTimestamp());
        self::assertSame(['key' => 'value'], $events[0]->getData());
        self::assertSame(['comacme' => 'foo'], $events[0]->getExtensions());
    }

    public function testUnmarshalBinaryRequestInvalidContentType(): void
    {
        $this->expectException(UnsupportedContentTypeException::class);

        Unmarshaller::createJsonUnmarshaller()->unmarshal(
            Message::parseRequest(
                "GET /endpoint HTTP/1.1\r\nHost: example.com\r\nContent-Type: application/xml\r\nContent-Length: 4\r\nce-specversion: 1.0\r\nce-id: 1234-1234-1234\r\nce-source: %2Fvar%2Fdata\r\nce-type: com.example.someevent\r\nce-dataschema: com.example%2Fschema\r\nce-subject: larger-context\r\nce-time: 2018-04-05T17%3A31%3A00Z\r\nce-comacme: foo\r\n\r\nDATA"
            )
        );
    }

    public function testUnmarshalBatchRequest(): void
    {
        $events = Unmarshaller::createJsonUnmarshaller()->unmarshal(
            Message::parseRequest(
                "GET /endpoint HTTP/1.1\r\nHost: example.com\r\nContent-Type: application/cloudevents-batch+json\r\nContent-Length: 535\r\n\r\n[{\"specversion\":\"1.0\",\"id\":\"1234-1234-1234\",\"source\":\"\/var\/data\",\"type\":\"com.example.someevent\",\"datacontenttype\":\"application\/json\",\"dataschema\":\"com.example\/schema\",\"subject\":\"larger-context\",\"time\":\"2018-04-05T17:31:00Z\",\"comacme\":\"foo\",\"data\":{\"key\":\"value\"}},{\"specversion\":\"1.0\",\"id\":\"1234-1234-2222\",\"source\":\"\/var\/data\",\"type\":\"com.example.someevent\",\"datacontenttype\":\"application\/json\",\"dataschema\":\"com.example\/schema\",\"subject\":\"larger-context\",\"time\":\"2018-04-05T17:31:00Z\",\"comacme\":\"foo\",\"data\":{\"key\":\"value\"}}]"
            )
        );

        self::assertCount(2, $events);

        self::assertInstanceOf(CloudEventInterface::class, $events[0]);
        self::assertSame('1.0', $events[0]->getSpecVersion());
        self::assertSame('1234-1234-1234', $events[0]->getId());
        self::assertSame('/var/data', $events[0]->getSource());
        self::assertSame('com.example.someevent', $events[0]->getType());
        self::assertSame('application/json', $events[0]->getDataContentType());
        self::assertSame('com.example/schema', $events[0]->getDataSchema());
        self::assertSame('larger-context', $events[0]->getSubject());
        self::assertSame(1522949460, $events[0]->getTime()->getTimestamp());
        self::assertSame(['key' => 'value'], $events[0]->getData());
        self::assertSame(['comacme' => 'foo'], $events[0]->getExtensions());

        self::assertInstanceOf(CloudEventInterface::class, $events[1]);
        self::assertSame('1.0', $events[1]->getSpecVersion());
        self::assertSame('1234-1234-2222', $events[1]->getId());
        self::assertSame('/var/data', $events[1]->getSource());
        self::assertSame('com.example.someevent', $events[1]->getType());
        self::assertSame('application/json', $events[1]->getDataContentType());
        self::assertSame('com.example/schema', $events[1]->getDataSchema());
        self::assertSame('larger-context', $events[1]->getSubject());
        self::assertSame(1522949460, $events[1]->getTime()->getTimestamp());
        self::assertSame(['key' => 'value'], $events[1]->getData());
        self::assertSame(['comacme' => 'foo'], $events[1]->getExtensions());
    }

    public function testUnmarshalStructuredResponse(): void
    {
        $events = Unmarshaller::createJsonUnmarshaller()->unmarshal(
            Message::parseResponse(
                "HTTP/1.1 200 OK\r\nContent-Type: application/cloudevents+json\r\nContent-Length: 266\r\n\r\n{\"specversion\":\"1.0\",\"id\":\"1234-1234-1234\",\"source\":\"\/var\/data\",\"type\":\"com.example.someevent\",\"datacontenttype\":\"application\/json\",\"dataschema\":\"com.example\/schema\",\"subject\":\"larger-context\",\"time\":\"2018-04-05T17:31:00Z\",\"comacme\":\"foo\",\"data\":{\"key\":\"value\"}}"
            )
        );

        self::assertCount(1, $events);

        self::assertInstanceOf(CloudEventInterface::class, $events[0]);
        self::assertSame('1.0', $events[0]->getSpecVersion());
        self::assertSame('1234-1234-1234', $events[0]->getId());
        self::assertSame('/var/data', $events[0]->getSource());
        self::assertSame('com.example.someevent', $events[0]->getType());
        self::assertSame('application/json', $events[0]->getDataContentType());
        self::assertSame('com.example/schema', $events[0]->getDataSchema());
        self::assertSame('larger-context', $events[0]->getSubject());
        self::assertSame(1522949460, $events[0]->getTime()->getTimestamp());
        self::assertSame(['key' => 'value'], $events[0]->getData());
        self::assertSame(['comacme' => 'foo'], $events[0]->getExtensions());
    }

    public function testUnmarshalStructuredResponseInvalidContentType(): void
    {
        $this->expectException(UnsupportedContentTypeException::class);

        Unmarshaller::createJsonUnmarshaller()->unmarshal(
            Message::parseResponse(
                "HTTP/1.1 200 OK\r\nContent-Type: application/cloudevents+xml\r\nContent-Length: 4\r\n\r\nDATA"
            )
        );
    }

    public function testUnmarshalBinaryResponse(): void
    {
        $events = Unmarshaller::createJsonUnmarshaller()->unmarshal(
            Message::parseResponse(
                "HTTP/1.1 200 OK\r\nContent-Type: application/json\r\nContent-Length: 15\r\nce-specversion: 1.0\r\nce-id: 1234-1234-1234\r\nce-source: %2Fvar%2Fdata\r\nce-type: com.example.someevent\r\nce-dataschema: com.example%2Fschema\r\nce-subject: larger-context\r\nce-time: 2018-04-05T17%3A31%3A00Z\r\nce-comacme: foo\r\n\r\n{\"key\":\"value\"}"
            )
        );

        self::assertCount(1, $events);

        self::assertInstanceOf(CloudEventInterface::class, $events[0]);
        self::assertSame('1.0', $events[0]->getSpecVersion());
        self::assertSame('1234-1234-1234', $events[0]->getId());
        self::assertSame('/var/data', $events[0]->getSource());
        self::assertSame('com.example.someevent', $events[0]->getType());
        self::assertSame('application/json', $events[0]->getDataContentType());
        self::assertSame('com.example/schema', $events[0]->getDataSchema());
        self::assertSame('larger-context', $events[0]->getSubject());
        self::assertSame(1522949460, $events[0]->getTime()->getTimestamp());
        self::assertSame(['key' => 'value'], $events[0]->getData());
        self::assertSame(['comacme' => 'foo'], $events[0]->getExtensions());
    }

    public function testUnmarshalBatchResponse(): void
    {
        $events = Unmarshaller::createJsonUnmarshaller()->unmarshal(
            Message::parseResponse(
                "HTTP/1.1 200 OK\r\nContent-Type: application/cloudevents-batch+json\r\nContent-Length: 535\r\n\r\n[{\"specversion\":\"1.0\",\"id\":\"1234-1234-1234\",\"source\":\"\/var\/data\",\"type\":\"com.example.someevent\",\"datacontenttype\":\"application\/json\",\"dataschema\":\"com.example\/schema\",\"subject\":\"larger-context\",\"time\":\"2018-04-05T17:31:00Z\",\"comacme\":\"foo\",\"data\":{\"key\":\"value\"}},{\"specversion\":\"1.0\",\"id\":\"1234-1234-2222\",\"source\":\"\/var\/data\",\"type\":\"com.example.someevent\",\"datacontenttype\":\"application\/json\",\"dataschema\":\"com.example\/schema\",\"subject\":\"larger-context\",\"time\":\"2018-04-05T17:31:00Z\",\"comacme\":\"foo\",\"data\":{\"key\":\"value\"}}]"
            )
        );

        self::assertCount(2, $events);

        self::assertInstanceOf(CloudEventInterface::class, $events[0]);
        self::assertSame('1.0', $events[0]->getSpecVersion());
        self::assertSame('1234-1234-1234', $events[0]->getId());
        self::assertSame('/var/data', $events[0]->getSource());
        self::assertSame('com.example.someevent', $events[0]->getType());
        self::assertSame('application/json', $events[0]->getDataContentType());
        self::assertSame('com.example/schema', $events[0]->getDataSchema());
        self::assertSame('larger-context', $events[0]->getSubject());
        self::assertSame(1522949460, $events[0]->getTime()->getTimestamp());
        self::assertSame(['key' => 'value'], $events[0]->getData());
        self::assertSame(['comacme' => 'foo'], $events[0]->getExtensions());

        self::assertInstanceOf(CloudEventInterface::class, $events[1]);
        self::assertSame('1.0', $events[1]->getSpecVersion());
        self::assertSame('1234-1234-2222', $events[1]->getId());
        self::assertSame('/var/data', $events[1]->getSource());
        self::assertSame('com.example.someevent', $events[1]->getType());
        self::assertSame('application/json', $events[1]->getDataContentType());
        self::assertSame('com.example/schema', $events[1]->getDataSchema());
        self::assertSame('larger-context', $events[1]->getSubject());
        self::assertSame(1522949460, $events[1]->getTime()->getTimestamp());
        self::assertSame(['key' => 'value'], $events[1]->getData());
        self::assertSame(['comacme' => 'foo'], $events[1]->getExtensions());
    }
}
