<?php

declare(strict_types=1);

namespace CloudEvents\V1;

use DateTimeImmutable;

interface CloudEventInterface extends \CloudEvents\CloudEventInterface
{
    public const SPEC_VERSION = '1.0';

    public function getId(): string;

    public function getSource(): string;

    public function getType(): string;

    /**
     * @return mixed
     */
    public function getData();

    public function getDataContentType(): ?string;

    public function getDataSchema(): ?string;

    public function getSubject(): ?string;

    public function getTime(): ?DateTimeImmutable;

    /**
     * @return array<string,bool|int|string>
     */
    public function getExtensions(): array;

    /**
     * @return bool|int|string|null
     */
    public function getExtension(string $attribute);
}
