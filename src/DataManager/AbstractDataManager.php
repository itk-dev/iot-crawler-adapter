<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataManager;

use App\Entity\Measurement;

abstract class AbstractDataManager
{
    abstract public function getAttributes(Measurement $measurement);
}
