<?php

declare(strict_types=1);

namespace CloudEvents\Utilities;

use ValueError;

/**
 * @internal
 */
final class DataFormatter
{
    /**
     * @param mixed $data
     *
     * @return array{data?: mixed, data_base64?: string}
     */
    public static function encode($data, bool $rawData): array
    {
        if (!$rawData && self::isBinary($data)) {
            /** @var string $data */
            return ['data_base64' => base64_encode($data)];
        }

        return $data !== null ? ['data' => $data] : [];
    }

    /**
     * @return mixed
     */
    public static function decode(array $data)
    {
        if (isset($data['data_base64'])) {
            if (!is_string($data['data_base64'])) {
                throw new ValueError(
                    \sprintf('%s(): Argument #1 ($data) contains bad data_base64 attribute content', __METHOD__)
                );
            }

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
    private static function isBinary($data): bool
    {
        return is_string($data) && !preg_match('//u', $data);
    }
}
