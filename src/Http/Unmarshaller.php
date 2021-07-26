<?php

declare(strict_types=1);

namespace CloudEvents\Http;

use CloudEvents\CloudEventInterface;
use CloudEvents\Serializers\JsonDeserializer;
use CloudEvents\Serializers\DeserializerInterface;
use Psr\Http\Message\MessageInterface;
use CloudEvents\Exceptions\InvalidPayloadSyntaxException;
use CloudEvents\Exceptions\UnsupportedContentTypeException;
use CloudEvents\Exceptions\UnsupportedSpecVersionException;
use CloudEvents\Exceptions\MissingAttributeException;

final class Unmarshaller implements UnmarshallerInterface
{
    /**
     * @var array<string, array{deserializer: DeserializerInterface, contentTypes: list<string>}>
     */
    private array $configuration;

    /**
     * @param array<string, array{deserializer: DeserializerInterface, contentTypes: list<string>}> $configuration
     */
    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    public static function createJsonUnmarshaller(): self
    {
        return new self([
            'json' => ['deserializer' => JsonDeserializer::create(), 'contentTypes' => ['application/json']],
        ]);
    }

    /**
     * @throws InvalidPayloadSyntaxException
     * @throws UnsupportedContentTypeException
     * @throws UnsupportedSpecVersionException
     * @throws MissingAttributeException
     *
     * @return list<CloudEventInterface>
     */
    public function unmarshal(MessageInterface $message): array
    {
        $contentType = strtolower(explode(';', $message->getHeader('Content-Type')[0] ?? '')[0]);

        foreach ($this->configuration as $type => $entry) {
            switch ($contentType) {
                case sprintf('application/cloudevents+%s', $type):
                    return self::unmarshalStructured($message, $entry['deserializer']);
                case sprintf('application/cloudevents-batch+%s', $type):
                    return self::unmarshalBatch($message, $entry['deserializer']);
                default:
                    if (in_array($contentType, $entry['contentTypes'], true)) {
                        return self::unmarshalBinary($message, $entry['deserializer']);
                    }
            }
        }

        throw new UnsupportedContentTypeException();
    }

    /**
     * @throws InvalidPayloadSyntaxException
     * @throws UnsupportedSpecVersionException
     * @throws MissingAttributeException
     *
     * @return list<CloudEventInterface>
     */
    private static function unmarshalStructured(
        MessageInterface $message,
        DeserializerInterface $deserializer
    ): array {
        $cloudEvent = $deserializer->deserializeStructured(
            (string) $message->getBody()
        );

        return [$cloudEvent];
    }

    /**
     * @throws InvalidPayloadSyntaxException
     * @throws UnsupportedSpecVersionException
     * @throws MissingAttributeException
     *
     * @return list<CloudEventInterface>
     */
    private static function unmarshalBinary(
        MessageInterface $message,
        DeserializerInterface $deserializer
    ): array {
        /** @psalm-suppress MixedArgumentTypeCoercion */
        $cloudEvent = $deserializer->deserializeBinary(
            (string) $message->getBody(),
            implode(', ', $message->getHeader('Content-Type')),
            self::decodeAttributes($message->getHeaders())
        );

        return [$cloudEvent];
    }

    /**
     * @param array<string, list<string>> $headers
     *
     * @return array<string, string>
     */
    private static function decodeAttributes(array $headers): array
    {
        $attributes = [];

        foreach ($headers as $key => $values) {
            /** @psalm-suppress UndefinedFunction */
            if (\str_starts_with($key, 'ce-')) {
                $attributes[substr($key, 3)] = implode(', ', $values);
            }
        }

        return $attributes;
    }

    /**
     * @throws InvalidPayloadSyntaxException
     * @throws UnsupportedSpecVersionException
     * @throws MissingAttributeException
     *
     * @return list<CloudEventInterface>
     */
    private static function unmarshalBatch(
        MessageInterface $message,
        DeserializerInterface $deserializer
    ): array {
        return $deserializer->deserializeBatch(
            (string) $message->getBody()
        );
    }
}
