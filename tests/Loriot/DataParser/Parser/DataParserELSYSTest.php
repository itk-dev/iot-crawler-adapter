<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\Loriot\DataParser\Parser;

class DataParserELSYSTest extends AbstractDataParserTestCase
{
    /**
     * @dataProvider parseDataProvider
     */
    public function testParse(string $data, $expected)
    {
        $parserId = 'ELSYS';
        $parser = $this->getParser($parserId);
        $actual = $parser->parse($data);

        $this->assertEquals($expected, $actual);
    }

    public function parseDataProvider(): array
    {
        return [
            [
                '010033025c070e1314000f703f',
                [
                    'temperature' => 5.1,
                    'humidity' => 92,
                    'vdd' => 3603,
                    'pressure' => 1011.775,
                ],
            ],
        ];
    }
}
