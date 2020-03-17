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

abstract class AbstractDataParser
{
    /** @var string */
    public static $id;

    protected $ignoreFieldsPattern = '/^header_frame_|_sensor_id$/';

    public function __construct()
    {
        if (empty(static::$id) || !\is_string(static::$id)) {
            throw new DataParserException(sprintf('Missing or invalid parser id in parser %s: %s', static::class, json_encode(static::$id)));
        }
    }

    public function getId()
    {
        return static::$id;
    }

    public function parse(string $data, bool $full = false): array
    {
        $format = $this->getFormat();

        $result = unpack($format, hex2bin($data));

        if (!$full) {
            if (isset($this->ignoreFieldsPattern)) {
                $result = array_filter(
                    $result,
                    function ($field) {
                        return !preg_match($this->ignoreFieldsPattern, $field);
                    },
                    ARRAY_FILTER_USE_KEY
                );
            }
        }

        return $result;
    }

    /**
     * Get sensors from parsed payload.
     *
     * @return array
     */
    public function getSensors(array $data)
    {
        $sensors = [];

        // Assume that wa have data with two keys per sensor:
        //  «measurement»_sensor_id
        //  «measurement»_value
        foreach ($data as $key => $value) {
            if (preg_match('/^(?P<name>.+)_(?P<key>sensor_id|value)$/', $key, $matches)) {
                $sensors[$matches['name']][$matches['key']] = $value;
            }
        }

        if (empty($sensors)) {
            // Assume that we have sensor name => value data.
            foreach ($data as $name => $value) {
                $sensors[$name] = [
                    'sensor_id' => $name,
                    'value' => $value,
                ];
            }
        }

        return $sensors;
    }

    protected function getFormat(): string
    {
        throw new DataParserException(sprintf('%s.%s not implemented', static::class, __FUNCTION__));
    }

    // https://www.php.net/manual/en/function.pack.php
    protected const FORMAT_UINT8 = 'C';
    protected const FORMAT_UINT16LE = 'v';
    protected const FORMAT_UINT32LE = 'V';
    protected const FORMAT_FLOATLE = 'g';

    protected function buildFormat(array $spec): string
    {
        $format = [];
        foreach ($spec as [$type, $name]) {
            $format[] = $type.$name;
        }

        return implode('/', $format);
    }
}
