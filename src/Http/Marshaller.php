<?php

declare(strict_types=1);

namespace CloudEvents\Http;

use CloudEvents\CloudEventInterface;
use CloudEvents\Serializers\JsonSerializer;
use CloudEvents\Serializers\SerializerInterface;
use CloudEvents\Exceptions\UnsupportedSpecVersionException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Http\Discovery\Psr17FactoryDiscovery;

final class Marshaller implements MarshallerInterface
{
    /**
     * @var array{serializer: SerializerInterface, type: string}
     */
    private array $configuration;

    private RequestFactoryInterface $requestFactory;
    private ResponseFactoryInterface $responseFactory;
    private StreamFactoryInterface $streamFactory;

    /**
     * @param array{serializer: SerializerInterface, type: string} $configuration
     */
    public function __construct(
        array $configuration,
        RequestFactoryInterface $requestFactory,
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory
    ) {
        $this->configuration = $configuration;
        $this->requestFactory = $requestFactory;
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
    }

    public static function createJsonMarshaller(
        RequestFactoryInterface $requestFactory = null,
        ResponseFactoryInterface $responseFactory = null,
        StreamFactoryInterface $streamFactory = null
    ): self {
        return new self(
            ['serializer' => JsonSerializer::create(), 'type' => 'json'],
            $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory(),
            $responseFactory ?? Psr17FactoryDiscovery::findResponseFactory(),
            $streamFactory ?? Psr17FactoryDiscovery::findStreamFactory()
        );
    }

    /**
     * @param UriInterface|string $uri
     *
     * @throws UnsupportedSpecVersionException
     */
    public function marshalStructuredRequest(
        CloudEventInterface $cloudEvent,
        string $method,
        $uri
    ): RequestInterface {
        $serialized = $this->configuration['serializer']->serializeStructured($cloudEvent);

        return $this->requestFactory->createRequest($method, $uri)
            ->withBody($this->streamFactory->createStream($serialized))
            ->withHeader('Content-Type', sprintf('application/cloudevents+%s', $this->configuration['type']))
            ->withHeader('Content-Length', (string) strlen($serialized));
    }

    /**
     * @param UriInterface|string $uri
     *
     * @throws UnsupportedSpecVersionException
     */
    public function marshalBinaryRequest(
        CloudEventInterface $cloudEvent,
        string $method,
        $uri
    ): RequestInterface {
        $serialized = $this->configuration['serializer']->serializeBinary($cloudEvent);

        $request = $this->requestFactory->createRequest($method, $uri)
            ->withBody($this->streamFactory->createStream($serialized['data']))
            ->withHeader('Content-Type', $serialized['contentType'])
            ->withHeader('Content-Length', (string) strlen($serialized['data']));

        foreach ($serialized['attributes'] as $key => $value) {
            $request = $request->withHeader(sprintf('ce-%s', $key), $value);
        }

        return $request;
    }

    /**
     * @param list<CloudEventInterface> $cloudEvents
     * @param UriInterface|string $uri
     *
     * @throws UnsupportedSpecVersionException
     */
    public function marshalBatchRequest(
        array $cloudEvents,
        string $method,
        $uri
    ): RequestInterface {
        $serialized = $this->configuration['serializer']->serializeBatch($cloudEvents);

        return $this->requestFactory->createRequest($method, $uri)
            ->withBody($this->streamFactory->createStream($serialized))
            ->withHeader('Content-Type', sprintf('application/cloudevents-batch+%s', $this->configuration['type']))
            ->withHeader('Content-Length', (string) strlen($serialized));
    }

    /**
     * @throws UnsupportedSpecVersionException
     */
    public function marshalStructuredResponse(
        CloudEventInterface $cloudEvent,
        int $code = 200,
        string $reasonPhrase = ''
    ): ResponseInterface {
        $serialized = $this->configuration['serializer']->serializeStructured($cloudEvent);

        return $this->responseFactory->createResponse($code, $reasonPhrase)
            ->withBody($this->streamFactory->createStream($serialized))
            ->withHeader('Content-Type', sprintf('application/cloudevents+%s', $this->configuration['type']))
            ->withHeader('Content-Length', (string) strlen($serialized));
    }

    /**
     * @param UriInterface|string $uri
     *
     * @throws UnsupportedSpecVersionException
     */
    public function marshalBinaryResponse(
        CloudEventInterface $cloudEvent,
        int $code = 200,
        string $reasonPhrase = ''
    ): ResponseInterface {
        $serialized = $this->configuration['serializer']->serializeBinary($cloudEvent);

        $response = $this->responseFactory->createResponse($code, $reasonPhrase)
            ->withBody($this->streamFactory->createStream($serialized['data']))
            ->withHeader('Content-Type', $serialized['contentType'])
            ->withHeader('Content-Length', (string) strlen($serialized['data']));

        foreach ($serialized['attributes'] as $key => $value) {
            $response = $response->withHeader(sprintf('ce-%s', $key), $value);
        }

        return $response;
    }

    /**
     * @param list<CloudEventInterface> $cloudEvents
     *
     * @throws UnsupportedSpecVersionException
     */
    public function marshalBatchResponse(
        array $cloudEvents,
        int $code = 200,
        string $reasonPhrase = ''
    ): ResponseInterface {
        $serialized = $this->configuration['serializer']->serializeBatch($cloudEvents);

        return $this->responseFactory->createResponse($code, $reasonPhrase)
            ->withBody($this->streamFactory->createStream($serialized))
            ->withHeader('Content-Type', sprintf('application/cloudevents-batch+%s', $this->configuration['type']))
            ->withHeader('Content-Length', (string) strlen($serialized));
    }
}
