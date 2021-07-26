<?php

declare(strict_types=1);

namespace CloudEvents\V1;

use DateTimeImmutable;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class CloudEventImmutable implements CloudEventInterface
{
    use CloudEventTrait;

    public function withId(string $id): self
    {
        $cloudEvent = clone $this;

        $cloudEvent->setId($id);

        return $cloudEvent;
    }

    public function withSource(string $source): self
    {
        $cloudEvent = clone $this;

        $cloudEvent->setSource($source);

        return $cloudEvent;
    }

    public function withType(string $type): self
    {
        $cloudEvent = clone $this;

        $cloudEvent->setType($type);

        return $cloudEvent;
    }

    /**
     * @param mixed $data
     */
    public function withData($data): self
    {
        $cloudEvent = clone $this;

        $cloudEvent->setData($data);

        return $cloudEvent;
    }

    public function withDataContentType(?string $dataContentType): self
    {
        $cloudEvent = clone $this;

        $cloudEvent->setDataContentType($dataContentType);

        return $cloudEvent;
    }

    public function withDataSchema(?string $dataSchema): self
    {
        $cloudEvent = clone $this;

        $cloudEvent->setDataSchema($dataSchema);

        return $cloudEvent;
    }

    public function withSubject(?string $subject): self
    {
        $cloudEvent = clone $this;

        $cloudEvent->setSubject($subject);

        return $cloudEvent;
    }

    public function withTime(?DateTimeImmutable $time): self
    {
        $cloudEvent = clone $this;

        $cloudEvent->setTime($time);

        return $cloudEvent;
    }

    /**
     * @param array<string,bool|int|string|null> $extensions
     */
    public function withExtensions(array $extensions): self
    {
        $cloudEvent = clone $this;

        $cloudEvent->setExtensions($extensions);

        return $cloudEvent;
    }

    /**
     * @param bool|int|string|null $value
     */
    public function withExtension(string $attribute, $value): self
    {
        $cloudEvent = clone $this;

        $cloudEvent->setExtension($attribute, $value);

        return $cloudEvent;
    }
}
