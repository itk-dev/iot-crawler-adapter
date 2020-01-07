<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataParser;

use App\DataParser\Exception\DataParserException;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DataParserManager
{
    use ContainerAwareTrait;

    /** @var array */
    private $parsers;

    public function __construct(ContainerInterface $container, array $parsers)
    {
        $this->container = $container;
        $this->parsers = $parsers;
    }

    public function getParser(string $id)
    {
        if (!isset($this->parsers[$id])) {
            throw new DataParserException(sprintf('Invalid parser: %s', $id));
        }

        return $this->container->get($this->parsers[$id]);
    }
}
