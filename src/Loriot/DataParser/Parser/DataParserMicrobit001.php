<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Loriot\DataParser\Parser;

use App\Loriot\DataParser\Exception\DataParserException;

/**
 * @see https://www.elsys.se/en/elsys-payload/
 */
class DataParserMicrobit001 extends AbstractDataParser
{
    public static $id = 'microbit:001';

    public function parse(string $data, bool $full = false): array
    {
        $data = trim(hex2bin($data));

        $parts = explode(':', $data);
        if (6 === \count($parts)) {
            [$id, $latitude, $longitude, $timestamp, $soundMeasurement, $version] = $parts;

            // @TODO Validate parts
            return [
                'id' => $id,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'timestamp' => $timestamp,
                'sound_measurement' => (int) $soundMeasurement,
                'version' => $version,
            ];
        }

        throw new DataParserException(sprintf('Invalid data: %s', $data));
    }

    public function getSensors(array $data)
    {
        return array_filter($data, static function ($key) {
            return 'sound_measurement' === $key;
        }, ARRAY_FILTER_USE_KEY);
    }
}
