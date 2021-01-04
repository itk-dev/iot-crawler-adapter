<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\Loriot\DataParser\Parser;

class DataParser0004A30B001E307CTest extends AbstractDataParserTestCase
{
    /**
     * @dataProvider parseDataProvider
     */
    public function testParse(string $data, $expected)
    {
        $parserId = '0004A30B001E307C';
        $parser = $this->getParser($parserId);
        $actual = $parser->parse($data);

        $this->assertEquals($expected, $actual);
    }

    public function parseDataProvider(): array
    {
        return [
            [
                'ef193450ba23004a85eb0ec34c0000c8424d66e4bd474fca00',
                [
                    // 'header_frame_counter' => 239,
                    // 'header_frame_length' => 25,
                    // 'battery_sensor_id' => 52,
                    'battery_value' => 80,
                    // 'charging_power_sensor_id' => 186,
                    'charging_power_value' => 35,
                    // 'air_temperature_sensor_id' => 74,
                    'air_temperature_value' => -142.9199981689453,
                    // 'humidity_sensor_id' => 76,
                    'humidity_value' => 100,
                    // 'pressure_sensor_id' => 77,
                    'pressure_value' => 97224.796875,
                    // 'distance_to_water_sensor_id' => 79,
                    'distance_to_water_value' => 202,
                ],
            ],
        ];
    }
}
