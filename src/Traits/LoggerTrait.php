<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Traits;

use Psr\Log\LoggerAwareTrait;

trait LoggerTrait
{
    use LoggerAwareTrait;
    use \Psr\Log\LoggerTrait;

    public function log($level, $message, array $context = [])
    {
        if (null !== $this->logger) {
            \call_user_func_array([$this->logger, 'log'], \func_get_args());
        }
    }
}
