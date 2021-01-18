<?php

declare(strict_types=1);

namespace CloudEvents\V1;

interface CloudEventInterface extends \CloudEvents\CloudEventInterface
{
    public function getId(): string;

    public function getSource(): string;

    public function getType(): string;

    public function getDataContentType(): ?string;

    public function getDataSchema(): ?string;

    public function getSubject(): ?string;

    public function getTime(): ?\DateTimeInterface;

    /**
     * @return mixed|null
     */
    public function getData();
}
