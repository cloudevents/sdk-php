<?php

declare(strict_types=1);

namespace CloudEvents\V1;

use DateTimeInterface;

class CloudEvent implements CloudEventInterface
{
    private string $id;
    private string $source;
    private string $type;
    private ?string $dataContentType;
    private ?string $dataSchema;
    private ?string $subject;
    private ?DateTimeInterface $time;

    /** @var mixed|null */
    private $data;

    public function __construct(
        string $id,
        string $source,
        string $type,
        $data = null,
        ?string $dataContentType = null,
        ?string $dataSchema = null,
        ?string $subject = null,
        ?DateTimeInterface $time = null
    ) {
        $this->id = $id;
        $this->source = $source;
        $this->type = $type;
        $this->data = $data;
        $this->dataContentType = $dataContentType;
        $this->dataSchema = $dataSchema;
        $this->subject = $subject;
        $this->time = $time;
    }

    public function getSpecVersion(): string
    {
        return static::SPEC_VERSION;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): CloudEvent
    {
        $this->id = $id;

        return $this;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): CloudEvent
    {
        $this->source = $source;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): CloudEvent
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): CloudEvent
    {
        $this->data = $data;

        return $this;
    }

    public function getDataContentType(): ?string
    {
        return $this->dataContentType;
    }

    public function setDataContentType(?string $dataContentType): CloudEvent
    {
        $this->dataContentType = $dataContentType;

        return $this;
    }

    public function getDataSchema(): ?string
    {
        return $this->dataSchema;
    }

    public function setDataSchema(?string $dataSchema): CloudEvent
    {
        $this->dataSchema = $dataSchema;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): CloudEvent
    {
        $this->subject = $subject;

        return $this;
    }

    public function getTime(): ?DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(?DateTimeInterface $time): CloudEvent
    {
        $this->time = $time;

        return $this;
    }
}
