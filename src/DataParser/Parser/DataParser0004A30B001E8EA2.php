<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataParser\Parser;

class DataParser0004A30B001E8EA2 extends AbstractDataParser
{
    public static $id = '0004A30B001E8EA2';

    protected function getFormat(): string
    {
        return $this->buildFormat([
            [self::FORMAT_UINT8, 'header_frame_counter'],
            [self::FORMAT_UINT8, 'header_frame_length'],
            [self::FORMAT_UINT8, 'battery_sensor_id'],
            [self::FORMAT_UINT8, 'battery_value'],
            [self::FORMAT_UINT8, 'charging_power_sensor_id'],
            [self::FORMAT_UINT16LE, 'charging_power_value'],
            [self::FORMAT_UINT8, 'air_temperature_sensor_id'],
            [self::FORMAT_FLOATLE, 'air_temperature_value'],
            [self::FORMAT_UINT8, 'humidity_sensor_id'],
            [self::FORMAT_FLOATLE, 'humidity_value'],
            [self::FORMAT_UINT8, 'pressure_sensor_id'],
            [self::FORMAT_FLOATLE, 'pressure_value'],
            [self::FORMAT_UINT8, 'lux_sensor_id'],
            [self::FORMAT_UINT32LE, 'lux_value'],
            [self::FORMAT_UINT8, 'solar_radiation_sensor_id'],
            [self::FORMAT_FLOATLE, 'solar_radiation_value'],
            [self::FORMAT_UINT8, 'wind_speed_sensor_id'],
            [self::FORMAT_FLOATLE, 'wind_speed_value'],
            [self::FORMAT_UINT8, 'wind_vane_sensor_id'],
            [self::FORMAT_UINT8, 'wind_vane_value'],
            [self::FORMAT_UINT8, 'rain_sensor_id'],
            [self::FORMAT_FLOATLE, 'rain_value'],
        ]);
    }
}
