<?php

declare(strict_types=1);

namespace CloudEvents\Http;

use CloudEvents\CloudEventInterface;
use Psr\Http\Message\MessageInterface;
use CloudEvents\Exceptions\InvalidPayloadSyntaxException;
use CloudEvents\Exceptions\UnsupportedContentTypeException;
use CloudEvents\Exceptions\UnsupportedSpecVersionException;
use CloudEvents\Exceptions\MissingAttributeException;

interface UnmarshallerInterface
{
    /**
     * @throws InvalidPayloadSyntaxException
     * @throws UnsupportedContentTypeException
     * @throws UnsupportedSpecVersionException
     * @throws MissingAttributeException
     *
     * @return list<CloudEventInterface>
     */
    public function unmarshal(MessageInterface $message): array;
}
