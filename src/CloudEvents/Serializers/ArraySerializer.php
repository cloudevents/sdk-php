<?php

namespace CloudEvents\Serializers;

use CloudEvents\CloudEventInterface;
use CloudEvents\Serializers\Exceptions\UnsupportedEventSpecVersionException;
use CloudEvents\V1\CloudEventInterface as V1CloudEventInterface;
use DateTimeInterface;

use function array_merge;

class ArraySerializer
{
    /**
     * @throws UnsupportedEventSpecVersionException
     * @throws \JsonException
     */
    public function serialize(CloudEventInterface $cloudEvent): array
    {
        $payload = $this->createPayload($cloudEvent);
        
        return array_merge($payload, $this->formatData($cloudEvent->getData()));
    }

    /**
     * Get a JSON-serializable array representation of the CloudEvent.
     *
     * @throws UnsupportedEventSpecVersionException
     */
    protected function createPayload(CloudEventInterface $cloudEvent): array
    {
        if ($cloudEvent instanceof V1CloudEventInterface) {
            return [
                'specversion' => $cloudEvent->getSpecVersion(),
                'id' => $cloudEvent->getId(),
                'type' => $cloudEvent->getType(),
                'datacontenttype' => $cloudEvent->getDataContentType(),
                'dataschema' => $cloudEvent->getDataSchema(),
                'subject' => $cloudEvent->getSubject(),
                'time' => $this->formatTime($cloudEvent->getTime()),
            ];
        }

        throw new UnsupportedEventSpecVersionException();
    }

    private function formatTime(?DateTimeInterface $time): ?string
    {
        return $time === null ? null : $time->format(DATE_RFC3339);
    }

    /**
     * @param mixed|null $data
     */
    private function formatData($data): array
    {
        if ($this->isBinary($data)) {
            return ['data_base64' => base64_encode($data)];
        }

        return ['data' => $data];
    }

    /**
     * @param mixed|null $data
     */
    private function isBinary($data): bool
    {
        return is_string($data) && !preg_match('//u', $data);
    }
}
