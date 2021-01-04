<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\Loriot\DataParser\Parser;

class DataParserMicrobit001 extends AbstractDataParserTestCase
{
    /**
     * @dataProvider parseDataProvider
     */
    public function testParse(string $data, $expected)
    {
        $parserId = 'microbit:001';
        $parser = $this->getParser($parserId);
        $actual = $parser->parse($data);

        $this->assertEquals($expected, $actual);
    }

    public function parseDataProvider(): array
    {
        return [
            [
                '3234393133353136313237303a41412e414141413a4f4f2e4f4f4f4f3a545454545454545454543a3030323a303400',
                [
                    'id' => '249135161270',
                    'latitude' => 'AA.AAAA',
                    'longitude' => 'OO.OOOO',
                    'sound_measurement' => 2,
                    'timestamp' => 'TTTTTTTTTT',
                    'version' => '04',
                ],
            ],
        ];
    }
}
