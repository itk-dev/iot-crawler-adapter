<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Loriot\DataParser\Parser;

class DataParser0004A30B001E1694 extends AbstractDataParser
{
    public static $id = '0004A30B001E1694';

    protected function getFormat(): string
    {
        return $this->buildFormat([
            [self::FORMAT_UINT8, 'header_frame_counter'],
            [self::FORMAT_UINT8, 'header_frame_length'],
            [self::FORMAT_UINT8, 'battery_sensor_id'],
            [self::FORMAT_UINT8, 'battery_value'],
            [self::FORMAT_UINT8, 'charging_power_sensor_id'],
            [self::FORMAT_UINT16LE, 'charging_power_value'],
            [self::FORMAT_UINT8, 'water_temperature_sensor_id'],
            [self::FORMAT_FLOATLE, 'water_temperature_value'],
        ]);
    }
}
