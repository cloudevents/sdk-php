<?php

declare(strict_types=1);

namespace CloudEvents\V1;

use DateTimeInterface;
use TypeError;
use ValueError;

class CloudEvent implements CloudEventInterface
{
    private const RESERVED_ATTRIBUTES = [
        'specversion',
        'id',
        'source',
        'type',
        'data',
        'data_base64',
        'datacontenttype',
        'dataschema',
        'subject',
        'time',
    ];

    private string $id;
    private string $source;
    private string $type;
    private ?string $dataContentType;
    private ?string $dataSchema;
    private ?string $subject;
    private ?DateTimeInterface $time;

    /** @var mixed|null */
    private $data;

    /** @var array<string,bool|int|string> */
    private array $extensions;

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
        $this->extensions = [];
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
     * @param mixed|null $data
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

    /**
     * @return array<string,bool|int|string>
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }

    /**
     * @param bool|int|string|null $value
     */
    public function setExtension(string $attribute, $value): CloudEvent
    {
        if (\preg_match('/^[a-z0-9]+$/', $attribute) !== 1) {
            throw new ValueError(
                \sprintf('%s(): Argument #1 ($attribute) must match the regex [a-z0-9]+, %s given', __METHOD__, $attribute)
            );
        }

        if (in_array($attribute, self::RESERVED_ATTRIBUTES, true)) {
            throw new ValueError(
                \sprintf('%s(): Argument #1 ($attribute) must not be a reserved attribute, %s given', __METHOD__, $attribute)
            );
        }

        $type = \get_debug_type($value);
        $types = ['bool', 'int', 'string', 'null'];

        if (!in_array($type, $types, true)) {
            throw new TypeError(
                \sprintf('%s(): Argument #2 ($value) must be of type %s, %s given', __METHOD__, implode('|', $types), $type)
            );
        }

        if ($value === null) {
            unset($this->extensions[$attribute]);
        } else {
            $this->extensions[$attribute] = $value;
        }

        return $this;
    }
}
