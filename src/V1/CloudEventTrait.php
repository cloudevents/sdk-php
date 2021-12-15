<?php

declare(strict_types=1);

namespace CloudEvents\V1;

use DateTimeImmutable;
use TypeError;
use ValueError;

trait CloudEventTrait
{
    private string $id;

    private string $source;

    private string $type;

    /** @var mixed */
    private $data;

    private ?string $dataContentType;

    private ?string $dataSchema;

    private ?string $subject;

    private ?DateTimeImmutable $time;

    /** @var array<string,bool|int|string> */
    private array $extensions = [];

    /**
     * @param mixed $data
     * @param array<string,bool|int|string|null> $extensions
     */
    public function __construct(
        string $id,
        string $source,
        string $type,
        $data = null,
        ?string $dataContentType = null,
        ?string $dataSchema = null,
        ?string $subject = null,
        ?DateTimeImmutable $time = null,
        array $extensions = []
    ) {
        $this->setId($id);
        $this->setSource($source);
        $this->setType($type);
        $this->setData($data);
        $this->setDataContentType($dataContentType);
        $this->setDataSchema($dataSchema);
        $this->setSubject($subject);
        $this->setTime($time);
        $this->setExtensions($extensions);
    }

    public static function createFromInterface(CloudEventInterface $event): self
    {
        return new self(
            $event->getId(),
            $event->getSource(),
            $event->getType(),
            $event->getData(),
            $event->getDataContentType(),
            $event->getDataSchema(),
            $event->getSubject(),
            $event->getTime(),
            $event->getExtensions()
        );
    }

    public function getSpecVersion(): string
    {
        return CloudEventInterface::SPEC_VERSION;
    }

    public function getId(): string
    {
        return $this->id;
    }

    private function setId(string $id): self
    {
        if ('' === $id) {
            throw new ValueError(
                \sprintf('%s(): Argument #1 ($id) must be a non-empty string, "" given', __METHOD__)
            );
        }

        $this->id = $id;

        return $this;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    private function setSource(string $source): self
    {
        if ('' === $source) {
            throw new ValueError(
                \sprintf('%s(): Argument #1 ($source) must be a non-empty string, "" given', __METHOD__)
            );
        }

        $this->source = $source;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    private function setType(string $type): self
    {
        if ('' === $type) {
            throw new ValueError(
                \sprintf('%s(): Argument #1 ($type) must be a non-empty string, "" given', __METHOD__)
            );
        }

        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    private function setData($data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getDataContentType(): ?string
    {
        return $this->dataContentType;
    }

    private function setDataContentType(?string $dataContentType): self
    {
        if ($dataContentType !== null && \preg_match('#^[-\w]+/[-\w]+$#', $dataContentType) !== 1) {
            throw new ValueError(
                \sprintf('%s(): Argument #1 ($dataContentType) must be a valid mime-type string or null, "%s" given', __METHOD__, $dataContentType)
            );
        }

        $this->dataContentType = $dataContentType;

        return $this;
    }

    public function getDataSchema(): ?string
    {
        return $this->dataSchema;
    }

    private function setDataSchema(?string $dataSchema): self
    {
        if ('' === $dataSchema) {
            throw new ValueError(
                \sprintf('%s(): Argument #1 ($dataSchema) must be a non-empty string or null, "" given', __METHOD__)
            );
        }

        $this->dataSchema = $dataSchema;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    private function setSubject(?string $subject): self
    {
        if ('' === $subject) {
            throw new ValueError(
                \sprintf('%s(): Argument #1 ($subject) must be a non-empty string or null, "" given', __METHOD__)
            );
        }

        $this->subject = $subject;

        return $this;
    }

    public function getTime(): ?DateTimeImmutable
    {
        return $this->time;
    }

    private function setTime(?DateTimeImmutable $time): self
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
     * @return bool|int|string|null
     */
    public function getExtension(string $attribute)
    {
        return $this->extensions[$attribute] ?? null;
    }

    /**
     * @param array<string,bool|int|string|null> $extensions
     */
    private function setExtensions(array $extensions): self
    {
        foreach ($extensions as $attribute => $value) {
            $this->setExtension($attribute, $value);
        }

        return $this;
    }

    /**
     * @param bool|int|string|null $value
     */
    private function setExtension(string $attribute, $value): self
    {
        if (\preg_match('/^[a-z0-9]+$/', $attribute) !== 1) {
            throw new ValueError(
                \sprintf('%s(): Argument #1 ($attribute) must match the regex [a-z0-9]+, %s given', __METHOD__, $attribute)
            );
        }

        /** @var list<string> */
        static $reservedAttributes = [
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

        if (in_array($attribute, $reservedAttributes, true)) {
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
