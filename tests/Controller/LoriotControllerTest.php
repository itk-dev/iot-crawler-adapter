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

class LoriotControllerTest extends AbstractControllerTest
{
    public function testPush()
    {
        $response = $this->get('/devices');
        $actual = json_decode($response->getContent(), true);
        $expected = [];
        $this->assertEquals($actual, $expected);

        $response = $this->post('/loriot');
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());

        $response = $this->post('/loriot', [
            'headers' => [
                'authorization' => 'token api-test-loriot',
            ],
        ]);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(['title' => 'Missing data format'], $this->getJson($response));

        $response = $this->post('/loriot', [
            'query' => [
                'dataFormat' => 'ELSYS',
            ],
            'headers' => [
                'authorization' => 'token api-test-loriot',
                'content-type' => 'application/json',
            ],
            'body' => '[',
        ]);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(['title' => 'Syntax error'], $this->getJson($response));

        $response = $this->post('/loriot', [
            'query' => [
                'dataFormat' => 'ELSYS',
            ],
            'headers' => [
                'authorization' => 'token api-test-loriot',
            ],
            'json' => [],
        ]);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(['title' => 'Invalid payload'], $this->getJson($response));

        $response = $this->post('/loriot', [
            'query' => [
                'dataFormat' => 'ELSYS',
            ],
            'headers' => [
                'authorization' => 'token api-test-loriot',
            ],
            'json' => ['cmd' => 'gw'],
        ]);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(['title' => 'Invalid payload'], $this->getJson($response));

        $response = $this->post('/loriot', [
            'query' => [
                'dataFormat' => 'ELSYS',
            ],
            'headers' => [
                'authorization' => 'token api-test-loriot',
            ],
            'json' => [
                'data' => '010033025c070e1314000f703f',
                'ts' => 1515504296415,
                'EUI' => 'ELSYS-A81758FFFE03CFE0',
                'seqno' => 17863,
                'cmd' => 'gw',
            ],
        ]);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $response = $this->get('/devices');
        $actual = $this->getJson($response);
        $this->assertCount(1, $actual);

        $this->assertArrayHasKey('id', $actual[0]);
        $this->assertEquals('ELSYS-A81758FFFE03CFE0', $actual[0]['id']);

        $this->assertArrayHasKey('sensors', $actual[0]);
        $this->assertCount(4, $actual[0]['sensors']);

        // Handshake?
        $response = $this->post('/loriot', [
            'query' => [
                'dataFormat' => 'ELSYS',
            ],
            'headers' => [
                'authorization' => 'token api-test-loriot',
                'content-type' => 'application/json',
            ],
            'body' => '{"cmd":"txd","EUI":"A81758FFFE03CFE0","seqdn":3236,"seqq":3236,"ts":1578580357307}',
        ]);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        // Actual measure measurement.
        $response = $this->post('/loriot', [
            'query' => [
                'dataFormat' => 'ELSYS',
            ],
            'headers' => [
                'authorization' => 'token api-test-loriot',
                'content-type' => 'application/json',
            ],
            'body' => '{"cmd":"gw","seqno":17863,"EUI":"ELSYS-A81758FFFE03CFE0","ts":1515504296415,"fcnt":6525,"port":4,"freq":868300000,"toa":61,"dr":"SF7 BW125 4\\/5","ack":false,"gws":[{"rssi":-91,"snr":8,"ts":1515504296415,"time":"2018-01-09T13:24:56.309729091Z","rsig":[{"ant":0,"chan":6,"rssic":-91,"lsnr":8,"etime":"8zjbwTIrGpyQFIAefBxNKg==","rssis":-92,"rssisd":0,"ftime":-1,"foff":12554,"ft2d":863,"rfbsb":99,"rs2s1":160}],"gweui":"7076FFFFFF010B88"},{"rssi":-104,"snr":-8,"ts":1515504296442,"time":"2018-01-09T13:24:56.309732793Z","rsig":[{"ant":0,"chan":6,"rssic":-104,"lsnr":-8,"etime":"5G+AW\\/25fLBGfujK6CWV4A==","rssis":-113,"rssisd":1,"ftime":-1,"foff":12425,"ft2d":176,"rfbsb":100,"rs2s1":80}],"gweui":"7076FFFFFF010B32"}],"bat":255,"data":"010033025c070e1314000f703f"}',
        ]);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $response = $this->get('/measurements/ELSYS-A81758FFFE03CFE0/ELSYS-A81758FFFE03CFE0-sensor-humidity');
        $actual = $this->getJson($response);
        $this->assertArrayHasKey('attributes', $actual);
        $this->assertCount(1, $actual['attributes']);
        $this->assertArrayHasKey('timestamp', $actual['attributes'][0]);
        $this->assertEquals('2018-01-09T13:24:56+00:00', $actual['attributes'][0]['timestamp']);
        $this->assertArrayHasKey('humidity', $actual['attributes'][0]);
        $this->assertEquals(92, $actual['attributes'][0]['humidity']);

        $response = $this->get('/measurements/ELSYS-A81758FFFE03CFE0/ELSYS-A81758FFFE03CFE0-sensor-temperature');
        $actual = $this->getJson($response);
        $this->assertArrayHasKey('attributes', $actual);
        $this->assertCount(1, $actual['attributes']);
        $this->assertArrayHasKey('timestamp', $actual['attributes'][0]);
        $this->assertEquals('2018-01-09T13:24:56+00:00', $actual['attributes'][0]['timestamp']);
        $this->assertArrayHasKey('temperature', $actual['attributes'][0]);
        $this->assertEquals(5.1, $actual['attributes'][0]['temperature']);
    }
}
