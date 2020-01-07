<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataParser\Parser;

use App\DataParser\Exception\DataParserException;

abstract class AbstractDataParser
{
    /** @var string */
    public static $id;

    protected $ignoreFields = ['header_frame_counter', 'header_frame_length'];

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

    public function parse(string $data): array
    {
        $format = $this->getFormat();

        $result = unpack($format, hex2bin($data));

        foreach ($this->ignoreFields as $field) {
            unset($result[$field]);
        }

        return $result;
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
