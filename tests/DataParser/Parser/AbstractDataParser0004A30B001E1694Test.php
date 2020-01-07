<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\DataParser\Parser;

class AbstractDataParser0004A30B001E1694Test extends AbstractDataParserTestCase
{
    /**
     * @dataProvider parseDataProvider
     */
    public function testParse(string $data, $expected)
    {
        $parserId = '0004A30B001E1694';
        $parser = $this->getParser($parserId);
        $actual = $parser->parse($data);

        $this->assertEquals($expected, $actual);
    }

    public function parseDataProvider(): array
    {
        return [
            [
                '6a0c345dba00008600a1e440',
                [
                    'header_frame_counter' => 106,
                    'header_frame_length' => 12,
                    'battery_sensor_id' => 52,
                    'battery_value' => 93,
                    'charging_power_sensor_id' => 186,
                    'charging_power_value' => 0,
                    'water_temperature_sensor_id' => 134,
                    'water_temperature_value' => 7.1446533203125,
                ],
            ],
        ];
    }
}
