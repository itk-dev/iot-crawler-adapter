<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\Controller;

use App\Entity\Measurement;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
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
                'name' => 'l',
                'data' => '{"t":8.13,"mP1":4.08,"mP2":14.98,"mP4":23.60,"mPX":25.32,"nP0":6.18,"nP1":22.25,"nP2":31.90,"nP4":33.87,"nPX":34.19,"aPS":1.63,"p":1033.56,"b":82.05,"h":36.49,"uv":0,"lux":2126.52,"seq":1042,"lat":0,"lng":0,"alt":0,"SIV":0,"PDOP":9999}',
                'ttl' => 360,
                'published_at' => '2020-03-24T13:46:33.187Z',
                'device_id' => '2a001c000f47373432363933',
            ],
        ]);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $response = $this->get('/devices');
        $actual = $this->getJson($response);
        $this->assertCount(1, $actual);

        $this->assertArrayHasKey('id', $actual[0]);
        $this->assertEquals('2a001c000f47373432363933', $actual[0]['id']);

        $this->assertArrayHasKey('sensors', $actual[0]);
        $this->assertCount(22, $actual[0]['sensors']);

        $response = $this->post('/montem', [
            'headers' => [
                'authorization' => 'token api-test-montem',
            ],
            'json' => [
                'name' => 'l',
                'data' => '{"t":8.13,"mP1":4.08,"mP2":14.98,"mP4":23.60,"mPX":25.32,"nP0":6.18,"nP1":22.25,"nP2":31.90,"nP4":33.87,"nPX":34.19,"aPS":1.63,"p":1033.56,"b":82.05,"h":36.49,"uv":0,"lux":2126.52,"seq":1042,"lat":0,"lng":0,"alt":0,"SIV":0,"PDOP":9999}',
                'ttl' => 360,
                'published_at' => '2020-03-24T13:46:33.187Z',
                'device_id' => '87001c000f47373432363933',
            ],
        ]);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $response = $this->get('/devices');
        $actual = $this->getJson($response);
        $this->assertCount(2, $actual);

        $response = $this->get('/devices/87001c000f47373432363933');
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $actual = $this->getJson($response);
        $this->assertArrayHasKey('sensors', $actual);
        $this->assertCount(22, $actual['sensors']);
    }

    public function testPurge()
    {
        $maxNumberOfMeasurements = 100;

        $startDate = new DateTime();
        $date = clone $startDate;
        for ($i = 0; $i < $maxNumberOfMeasurements + 1; ++$i) {
            $response = $this->post('/montem', [
                'headers' => [
                    'authorization' => 'token api-test-montem',
                ],
                'json' => [
                    'name' => 'l',
                    'data' => '{"t":8.13,"mP1":4.08,"mP2":14.98,"mP4":23.60,"mPX":25.32,"nP0":6.18,"nP1":22.25,"nP2":31.90,"nP4":33.87,"nPX":34.19,"aPS":1.63,"p":1033.56,"b":82.05,"h":36.49,"uv":0,"lux":2126.52,"seq":1042,"lat":0,"lng":0,"alt":0,"SIV":0,"PDOP":9999}',
                    'ttl' => 360,
                    'published_at' => $date->format(DateTime::ATOM),
                    'device_id' => 'purge-test',
                ],
            ]);
            $date->add(new DateInterval('PT1H'));

            $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        }

        $response = $this->get('/devices');
        $actual = $this->getJson($response);
        $this->assertCount(1, $actual);

        $kernel = static::$kernel ?? self::bootKernel();

        /** @var EntityManager $entityManager */
        $entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        // Check that only $maxNumberOfMeasurements measurements are stored and that only the latest are stored.
        $rows = $entityManager->createQueryBuilder()
            ->select('IDENTITY(m.sensor) AS sensor_id, m.timestamp AS timestamp')
            ->from(Measurement::class, 'm')
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $sensorMeasurements = [];
        foreach ($rows as $row) {
            $sensorMeasurements[$row['sensor_id']][] = $row['timestamp'];
        }

        foreach ($sensorMeasurements as $sensorId => $measurements) {
            $this->assertCount($maxNumberOfMeasurements, $measurements);
            $this->assertGreaterThan($startDate, min($measurements));
        }
    }
}
