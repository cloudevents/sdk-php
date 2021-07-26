<?php

declare(strict_types=1);

namespace CloudEvents\Http;

use CloudEvents\CloudEventInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use CloudEvents\Exceptions\UnsupportedSpecVersionException;

interface MarshallerInterface
{
    /**
     * @param UriInterface|string $uri
     *
     * @throws UnsupportedSpecVersionException
     */
    public function marshalStructuredRequest(
        CloudEventInterface $cloudEvent,
        string $method,
        $uri
    ): RequestInterface;

    /**
     * @param UriInterface|string $uri
     *
     * @throws UnsupportedSpecVersionException
     */
    public function marshalBinaryRequest(
        CloudEventInterface $cloudEvent,
        string $method,
        $uri
    ): RequestInterface;

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
    ): RequestInterface;

    /**
     * @throws UnsupportedSpecVersionException
     */
    public function marshalStructuredResponse(
        CloudEventInterface $cloudEvent,
        int $code = 200,
        string $reasonPhrase = ''
    ): ResponseInterface;

    /**
     * @throws UnsupportedSpecVersionException
     */
    public function marshalBinaryResponse(
        CloudEventInterface $cloudEvent,
        int $code = 200,
        string $reasonPhrase = ''
    ): ResponseInterface;

    /**
     * @param list<CloudEventInterface> $cloudEvents
     *
     * @throws UnsupportedSpecVersionException
     */
    public function marshalBatchResponse(
        array $cloudEvents,
        int $code = 200,
        string $reasonPhrase = ''
    ): ResponseInterface;
}
