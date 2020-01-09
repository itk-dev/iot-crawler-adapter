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
        $this->assertEquals(['title' => 'Undefined index: EUI'], $this->getJson($response));

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
    }

    private $client;

    private function request(string $method, string $uri, array $options = [])
    {
        if (null === $this->client) {
            $this->client = static::createClient();
        }

        $parameters = $options['query'] ?? [];
        $files = [];
        $server = [];
        $headers = $options['headers'] ?? [];
        if (\array_key_exists('json', $options) && !\in_array('content-type', array_map('strtolower', array_keys($headers)), true)) {
            $headers['content-type'] = 'application/json';
        }
        foreach ($headers as $name => $value) {
            $name = strtoupper(preg_replace('/[^a-z0-9]/i', '_', $name));
            if (!\in_array($name, ['CONTENT_TYPE'], true)) {
                $name = 'HTTP_'.$name;
            }
            $server[$name] = $value;
        }

        $content = \array_key_exists('json', $options) ? json_encode($options['json']) : ($options['body'] ?? null);

        $this->client->request($method, $uri, $parameters, $files, $server, $content);

        return $this->client->getResponse();
    }

    private function get(string $uri, array $options = [])
    {
        return $this->request('GET', $uri, $options);
    }

    private function post(string $uri, array $options = [])
    {
        return $this->request('POST', $uri, $options);
    }

    private function getJson(Response $response)
    {
        return json_decode($response->getContent(), true);
    }
}
