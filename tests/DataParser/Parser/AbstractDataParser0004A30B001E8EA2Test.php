<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\DataParser\Parser;

class AbstractDataParser0004A30B001E8EA2Test extends AbstractDataParserTestCase
{
    /**
     * @dataProvider parseDataProvider
     */
    public function testParse(string $data, $expected)
    {
        $parserId = '0004A30B001E8EA2';
        $parser = $this->getParser($parserId);
        $actual = $parser->parse($data);

        $this->assertEquals($expected, $actual);
    }

    public function parseDataProvider(): array
    {
        return [
            [
                'b92c345fba00004a713d10c34c0000c8424d6cbb9a474e280d0000a17db07c419c9a9919409d069f00000000',
                [
                    'header_frame_counter' => 185,
                    'header_frame_length' => 44,
                    'battery_sensor_id' => 52,
                    'battery_value' => 95,
                    'charging_power_sensor_id' => 186,
                    'charging_power_value' => 0,
                    'air_temperature_sensor_id' => 74,
                    'air_temperature_value' => -144.24000549316406,
                    'humidity_sensor_id' => 76,
                    'humidity_value' => 100,
                    'pressure_sensor_id' => 77,
                    'pressure_value' => 79222.84375,
                    'lux_sensor_id' => 78,
                    'lux_value' => 3368,
                    'solar_radiation_sensor_id' => 161,
                    'solar_radiation_value' => 15.79308795928955,
                    'wind_speed_sensor_id' => 156,
                    'wind_speed_value' => 2.4000000953674316,
                    'wind_vane_sensor_id' => 157,
                    'wind_vane_value' => 6,
                    'rain_sensor_id' => 159,
                    'rain_value' => 0,
                ],
            ],
        ];
    }
}
