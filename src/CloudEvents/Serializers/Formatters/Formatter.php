<?php

namespace CloudEvents\Serializers\Formatters;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use ValueError;

class Formatter implements FormatterInterface
{
    private const TIME_FORMAT = 'Y-m-d\TH:i:s\Z';
    private const TIME_ZONE = 'UTC';

    public function encodeTime(?DateTimeInterface $time): ?string
    {
        if ($time === null) {
            return null;
        }

        // make sure we don't mutate the original object
        return ($time instanceof DateTime ? DateTimeImmutable::createFromMutable($time) : $time)
            ->setTimezone(new DateTimeZone(self::TIME_ZONE))
            ->format(self::TIME_FORMAT);
    }

    public function decodeTime(?string $time): ?DateTimeInterface
    {
        $parsed = DateTimeImmutable::createFromFormat(self::TIME_FORMAT, $time, new DateTimeZone(self::TIME_ZONE));

        return $parsed === false
            ? null
            : $parsed;
    }

    /**
     * @param mixed|null $data
     */
    public function encodeData($data): array
    {
        if ($this->isBinary($data)) {
            return ['data_base64' => base64_encode($data)];
        }

        return ['data' => $data];
    }

    /**
     * @return mixed|null
     */
    public function decodeData(array $data)
    {
        if (isset($data['data_base64'])) {
            $decoded = base64_decode($data['data_base64'], true);

            if ($decoded === false) {
                throw new ValueError(
                    \sprintf('%s::decodeData(): Argument #1 ($data) contains bad data_base64 attribute content', self::class)
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
     * @param mixed|null $data
     */
    protected function isBinary($data): bool
    {
        return is_string($data) && !preg_match('//u', $data);
    }
}
