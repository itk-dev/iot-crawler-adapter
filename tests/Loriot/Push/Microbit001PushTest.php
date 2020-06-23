<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\Loriot\Push;

use Symfony\Component\HttpFoundation\Response;

class Microbit001PushTest extends AbstractPushTest
{
    public function pushDataProvider(): array
    {
        return [
            [
                [
                    'payload' => '',
                ],
                [
                    'status_code' => Response::HTTP_UNAUTHORIZED,
                ],
            ],

            [
                [
                    'payload' => '',
                    'api_token' => 'api-test-loriot',
                ],
                [
                    'status_code' => Response::HTTP_BAD_REQUEST,
                ],
            ],

            [
                [
                    'payload' => '{}',
                    'api_token' => 'api-test-loriot',
                ],
                [
                    'status_code' => Response::HTTP_BAD_REQUEST,
                ],
            ],
        ];
    }
}
