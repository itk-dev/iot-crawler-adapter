<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AbstractControllerTest extends WebTestCase
{
    /** @var KernelBrowser */
    private $client;

    protected function request(string $method, string $uri, array $options = [])
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

        $content = \array_key_exists('json', $options) ? json_encode($options['json'], JSON_THROW_ON_ERROR, 512) : ($options['body'] ?? null);

        $this->client->request($method, $uri, $parameters, $files, $server, $content);

        return $this->client->getResponse();
    }

    protected function get(string $uri, array $options = [])
    {
        return $this->request('GET', $uri, $options);
    }

    protected function post(string $uri, array $options = [])
    {
        return $this->request('POST', $uri, $options);
    }

    protected function getJson(Response $response)
    {
        return json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }
}
