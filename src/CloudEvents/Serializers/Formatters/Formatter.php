<?php

namespace CloudEvents\Serializers\Formatters;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use ValueError;

class Formatter implements FormatterInterface
{
    private const TIME_FORMAT = 'Y-m-d\TH:i:s\Z';
    private const TIME_ZONE = 'UTC';

    public function encodeTime(?DateTimeImmutable $time): ?string
    {
        if ($time === null) {
            return null;
        }

        return $time->setTimezone(new DateTimeZone(self::TIME_ZONE))->format(self::TIME_FORMAT);
    }

    public function decodeTime(?string $time): ?DateTimeImmutable
    {
        if ($time === null) {
            return null;
        }

        $decoded = DateTimeImmutable::createFromFormat(self::TIME_FORMAT, $time, new DateTimeZone(self::TIME_ZONE));

        if ($decoded === false) {
              throw new ValueError(
                  \sprintf('%s(): Argument #1 ($time) is not a valid RFC3339 timestamp', __METHOD__)
              );
        }

        return $decoded;
    }

    /**
     * @param mixed $data
     */
    public function encodeData($data): array
    {
        if ($this->isBinary($data)) {
            return ['data_base64' => base64_encode($data)];
        }

        return ['data' => $data];
    }

    /**
     * @return mixed
     */
    public function decodeData(array $data)
    {
        if (isset($data['data_base64'])) {
            $decoded = base64_decode($data['data_base64'], true);

            if ($decoded === false) {
                throw new ValueError(
                    \sprintf('%s(): Argument #1 ($data) contains bad data_base64 attribute content', __METHOD__)
                );
            }

            return $decoded;
        }

        if (isset($data['data'])) {
            return $data['data'];
        }

        return null;
    }

    /**
     * @param mixed $data
     */
    protected function isBinary($data): bool
    {
        return is_string($data) && !preg_match('//u', $data);
    }
}
