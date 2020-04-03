<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

class MontemControllerTest extends AbstractControllerTest
{
    public function testPush()
    {
        $response = $this->get('/devices');
        $actual = json_decode($response->getContent(), true);
        $expected = [];
        $this->assertEquals($actual, $expected);

        $response = $this->post('/montem');
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());

        $response = $this->post('/montem', [
            'headers' => [
                'authorization' => 'token api-test-montem',
            ],
        ]);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(['title' => 'Invalid content-type: application/x-www-form-urlencoded'], $this->getJson($response));

        $response = $this->post('/montem', [
            'headers' => [
                'authorization' => 'token api-test-montem',
                'content-type' => 'application/json',
            ],
            'body' => '[',
        ]);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(['title' => 'Syntax error'], $this->getJson($response));

        $response = $this->post('/montem', [
            'headers' => [
                'authorization' => 'token api-test-montem',
            ],
            'json' => [],
        ]);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(['title' => 'Invalid payload'], $this->getJson($response));

        $response = $this->post('/montem', [
            'headers' => [
                'authorization' => 'token api-test-montem',
            ],
            'json' => ['id' => 'montem-001'],
        ]);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(['title' => 'Invalid payload'], $this->getJson($response));

        $response = $this->post('/montem', [
            'headers' => [
                'authorization' => 'token api-test-montem',
            ],
            'json' => [
                'id' => 'montem-001',
                'uuid' => 'montem-001',
                'data' => [],
            ],
        ]);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $response = $this->get('/devices');
        $actual = $this->getJson($response);
        $this->assertCount(1, $actual);

        $this->assertArrayHasKey('id', $actual[0]);
        $this->assertEquals('montem-001', $actual[0]['id']);

        $this->assertArrayHasKey('sensors', $actual[0]);
        $this->assertCount(0, $actual[0]['sensors']);
    }
}
