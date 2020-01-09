<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoriotControllerTest extends WebTestCase
{
    public function testPush()
    {
        $client = static::createClient();

        $client->request('GET', '/devices');
        $actual = json_decode($client->getResponse()->getContent(), true);
        $expected = [];
        $this->assertEquals($actual, $expected);

        $client->request('POST', '/loriot');
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());

        $client->request('POST', '/loriot', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'token api-test-loriot',
        ]);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $this->assertEquals(['title' => 'Missing data format'], json_decode($client->getResponse()->getContent(), true));

        $client->request('POST', '/loriot', [
            'dataFormat' => 'ELSYS',
        ], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'token api-test-loriot',
        ]);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $this->assertEquals(['title' => 'Syntax error'], json_decode($client->getResponse()->getContent(), true));

        $client->request('POST', '/loriot', [
            'dataFormat' => 'ELSYS',
        ], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'token api-test-loriot',
        ], json_encode([]));
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $this->assertEquals(['title' => 'Undefined index: EUI'], json_decode($client->getResponse()->getContent(), true));

        $client->request('POST', '/loriot', [
            'dataFormat' => 'ELSYS',
        ], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'token api-test-loriot',
        ], file_get_contents(__DIR__.'/../../src/DataFixtures/Loriot/payload/ELSYS/test.json'));
        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $client->request('GET', '/devices');
        $actual = json_decode($client->getResponse()->getContent(), true);
        $expected = [];
        $this->assertCount(1, $actual);
    }
}
