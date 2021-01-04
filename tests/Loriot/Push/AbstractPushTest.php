<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\Loriot\Push;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class AbstractPushTest extends TestCase
{
    /** @var HttpClient */
    protected static $client;

    public static function setUpBeforeClass()
    {
        static::$client = HttpClient::create([
            'base_uri' => 'https://127.0.0.1:8000/',
            'verify_peer' => false,
        ]);
    }

    /**
     * @dataProvider pushDataProvider
     */
    public function testPush(array $request, $expected)
    {
        $headers = [
            'content-type' => $request['content-type'] ?? 'application/json',
        ];
        if (isset($request['api_token'])) {
            $headers['authorization'] = 'token '.$request['api_token'];
        }
        /** @var ResponseInterface $actual */
        $actual = static::$client->request('POST', '/loriot', [
            'body' => $request['payload'] ?? null,
            'headers' => $headers,
        ]);

        if (\array_key_exists('status_code', $expected)) {
            $this->assertEquals($expected['status_code'], $actual->getStatusCode());
        }
    }

    abstract public function pushDataProvider(): array;
}
