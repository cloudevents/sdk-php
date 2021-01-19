<?php

declare(strict_types=1);

namespace CloudEvents;

class CloudEvent
{
    public const VERSION_1_0 = '1.0';

    private string $id;
    private string $source;
    private string $specversion;
    private string $type;

    private $data;

    public function __construct()
    {
        $this->specVersion = self::VERSION_1_0;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(string $data): CloudEvent
    {
        $this->data = $data;

        return $this;
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

    public function getSpecVersion(): string
    {
        return $this->specVersion;
    }

    public function setSpecVersion(string $specVersion): CloudEvent
    {
        $this->specVersion = $specVersion;

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
}
