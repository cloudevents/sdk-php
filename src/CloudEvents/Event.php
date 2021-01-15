<?php

declare(strict_types=1);

namespace CloudEvents;

class Event
{
    public const VERSION_1_0 = '1.0';

    public function __construct()
    {
        $this->specVersion = self::VERSION_1_0;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(string $data): Event
    {
        $this->data = $data;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): Event
    {
        $this->id = $id;

        return $this;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): Event
    {
        $this->source = $source;

        return $this;
    }

    public function getSpecVersion(): string
    {
        return $this->specVersion;
    }

    public function setSpecVersion(string $specVersion): Event
    {
        $this->specVersion = $specVersion;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): Event
    {
        $this->type = $type;

        return $this;
    }
}
