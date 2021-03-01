<?php

namespace CloudEvents\Serializers;

use CloudEvents\CloudEventInterface;
use CloudEvents\Serializers\Exceptions\UnsupportedEventSpecVersionException;

class JsonSerializer
{
    protected ArraySerializer $arraySerializer;

    public function __construct(ArraySerializer $arraySerializer = null)
    {
        $this->arraySerializer = $arraySerializer ?? new ArraySerializer();
    }

    /**
     * @throws UnsupportedEventSpecVersionException
     * @throws \JsonException
     */
    public function serialize(CloudEventInterface $cloudEvent): string
    {
        return json_encode($this->arraySerializer->serialize($cloudEvent), JSON_THROW_ON_ERROR);
    }
}
