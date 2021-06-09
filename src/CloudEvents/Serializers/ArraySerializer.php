<?php

namespace CloudEvents\Serializers;

use CloudEvents\CloudEventInterface;
use CloudEvents\Serializers\Exceptions\MissingPayloadAttributeException;
use CloudEvents\Serializers\Exceptions\UnsupportedEventSpecVersionException;
use CloudEvents\Serializers\Formatters\Formatter;
use CloudEvents\Serializers\Formatters\FormatterInterface;
use CloudEvents\V1\CloudEventInterface as V1CloudEventInterface;
use CloudEvents\V1\CloudEvent;
use DateTimeInterface;
use DateTimeZone;

class ArraySerializer
{
    protected FormatterInterface $formatter;

    public function __construct(FormatterInterface $formatter = null)
    {
        $this->formatter = $formatter ?? new Formatter();
    }

    /**
     * @throws UnsupportedEventSpecVersionException
     */
    public function serialize(CloudEventInterface $cloudEvent): array
    {
        return array_filter(array_merge(
            $this->encodePayload($cloudEvent),
            $this->formatter->encodeData($cloudEvent->getData())
        ), fn ($attr) => $attr !== null);
    }

    /**
     * Get a JSON-serializable array representation of the CloudEvent.
     *
     * @throws UnsupportedEventSpecVersionException
     */
    protected function encodePayload(CloudEventInterface $cloudEvent): array
    {
        if ($cloudEvent instanceof V1CloudEventInterface) {
            return array_merge([
                'specversion' => $cloudEvent->getSpecVersion(),
                'id' => $cloudEvent->getId(),
                'source' => $cloudEvent->getSource(),
                'type' => $cloudEvent->getType(),
                'datacontenttype' => $cloudEvent->getDataContentType(),
                'dataschema' => $cloudEvent->getDataSchema(),
                'subject' => $cloudEvent->getSubject(),
                'time' => $this->formatter->encodeTime($cloudEvent->getTime()),
            ], $cloudEvent->getExtensions());
        }

        throw new UnsupportedEventSpecVersionException();
    }

    /**
     * @throws UnsupportedEventSpecVersionException
     * @throws MissingPayloadAttributeException
     */
    public function deserialize(array $payload): CloudEventInterface
    {
        return $this->decodePayload($payload)->setData($this->formatter->decodeData($payload));
    }

    /**
     * Get a CloudEvent from a JSON-serializable array representation.
     *
     * @throws UnsupportedEventSpecVersionException
     * @throws MissingPayloadAttributeException
     */
    protected function decodePayload(array $payload): CloudEventInterface
    {
        if ($payload['specversion'] ?? null === V1CloudEventInterface::SPEC_VERSION) {
            if (!isset($payload['id']) || !isset($payload['source']) || !isset($payload['type'])) {
                throw new MissingPayloadAttributeException();
            }

            $cloudEvent = new CloudEvent(
                $payload['id'],
                $payload['source'],
                $payload['type']
            );

            foreach ($payload as $attribute => $value) {
                switch ($attribute) {
                    case 'specversion':
                    case 'id':
                    case 'source':
                    case 'type':
                    case 'data':
                    case 'data_base64':
                        break;
                    case 'datacontenttype':
                        $cloudEvent->setDataContentType($value);
                        break;
                    case 'dataschema':
                        $cloudEvent->setDataSchema($value);
                        break;
                    case 'subject':
                        $cloudEvent->setSubject($value);
                        break;
                    case 'time':
                        $cloudEvent->setTime($this->formatter->decodeTime($value));
                        break;
                    default:
                        $cloudEvent->setExtension($attribute, $value);
                        break;
                }
            }

            return $cloudEvent;
        }

        throw new UnsupportedEventSpecVersionException();
    }
}
