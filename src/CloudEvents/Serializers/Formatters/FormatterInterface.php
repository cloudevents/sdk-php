<?php

namespace CloudEvents\Serializers\Formatters;

use DateTimeInterface;

interface FormatterInterface
{
    public function encodeTime(?DateTimeInterface $time): ?string;

    public function decodeTime(?string $time): ?DateTimeInterface;

    /**
     * @param mixed|null $data
     */
    public function encodeData($data): array;

    /**
     * @return mixed|null
     */
    public function decodeData(array $data);
}
