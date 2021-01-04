<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Loriot\DataParser;

use App\Loriot\DataParser\Exception\DataParserException;
use App\Loriot\DataParser\Parser\AbstractDataParser;

class DataParserManager
{
    /** @var array */
    private $parsers;

    public function __construct(iterable $loriotDataParsers)
    {
        foreach ($loriotDataParsers as $parser) {
            if ($parser instanceof AbstractDataParser) {
                $this->parsers[$parser->getId()] = $parser;
            }
        }
    }

    public function getParser(string $id): AbstractDataParser
    {
        if (!isset($this->parsers[$id])) {
            throw new DataParserException(sprintf('Invalid parser: %s', $id));
        }

        return $this->parsers[$id];
    }
}
