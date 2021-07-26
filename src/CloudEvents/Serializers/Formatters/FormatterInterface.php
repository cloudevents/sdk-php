<?php

namespace CloudEvents\Serializers\Formatters;

use DateTimeImmutable;

interface FormatterInterface
{
    public function encodeTime(?DateTimeImmutable $time): ?string;

    public function decodeTime(?string $time): ?DateTimeImmutable;

    /**
     * @param mixed $data
     */
    public function encodeData($data): array;

    /**
     * @return mixed
     */
    public function decodeData(array $data);
}
