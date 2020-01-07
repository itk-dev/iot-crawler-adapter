<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\DataParser\Parser;

use App\DataParser\DataParserManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractDataParserTestCase extends KernelTestCase
{
    protected function setUp()
    {
        self::bootKernel();
    }

    protected function getParser($id)
    {
        return self::$container->get(DataParserManager::class)->getParser($id);
    }
}
