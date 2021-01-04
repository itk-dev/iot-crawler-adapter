<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

class SmartCitizenControllerTest extends AbstractControllerTest
{
    public function testPush()
    {
        $response = $this->get('/devices');
        $actual = json_decode($response->getContent(), true);
        $expected = [];
        $this->assertEquals($actual, $expected);

        $response = $this->post('/smartcitizen');
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());

        $response = $this->post('/smartcitizen', [
            'headers' => [
                'authorization' => 'token api-test-smartcitizen',
            ],
        ]);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(['title' => 'Invalid content-type: application/x-www-form-urlencoded'], $this->getJson($response));

        $response = $this->post('/smartcitizen', [
            'headers' => [
                'authorization' => 'token api-test-smartcitizen',
                'content-type' => 'application/json',
            ],
            'body' => '[',
        ]);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(['title' => 'Syntax error'], $this->getJson($response));

        $response = $this->post('/smartcitizen', [
            'headers' => [
                'authorization' => 'token api-test-smartcitizen',
            ],
            'json' => [],
        ]);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(['title' => 'Invalid request'], $this->getJson($response));

        $response = $this->post('/smartcitizen', [
            'headers' => [
                'authorization' => 'token api-test-smartcitizen',
            ],
            'json' => ['id' => 'smartcitizen-001'],
        ]);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(['title' => 'Invalid request'], $this->getJson($response));

        $response = $this->post('/smartcitizen', [
            'headers' => [
                'authorization' => 'token api-test-smartcitizen',
            ],
            'json' => json_decode(<<<'JSON'
{
    "kit": {
        "updated_at": "2019-03-21T17:02:46Z",
        "created_at": "2019-03-21T17:02:46Z",
        "description": "Smart Citizen Kit 2.1 with Urban Sensor Board",
        "name": "SCK 2.1",
        "slug": "sck:2,1",
        "uuid": "56bec177-6d93-4001-b700-1abd8347ed87",
        "id": 26
    },
    "data": {
        "sensors": [
            {
                "prev_raw_value": 3,
                "prev_value": 3,
                "raw_value": 5,
                "value": 5,
                "uuid": "0c2a1afc-dc08-4066-aacb-0bde6a3ae6f5",
                "measurement_id": 47,
                "updated_at": "2019-03-21T16:43:37Z",
                "created_at": "2019-03-21T16:43:37Z",
                "unit": "ppb",
                "description": "Total Volatile Organic Compounds Digital Indoor Sensor",
                "name": "AMS CCS811 - TVOC",
                "ancestry": "111",
                "id": 113
            },
            {
                "prev_raw_value": 425,
                "prev_value": 425,
                "raw_value": 438,
                "value": 438,
                "uuid": "995343c9-12ac-40c0-b6b9-19699e524f86",
                "measurement_id": 46,
                "updated_at": "2019-03-21T16:43:37Z",
                "created_at": "2019-03-21T16:43:37Z",
                "unit": "ppm",
                "description": "Equivalent Carbon Dioxide Digital Indoor Sensor",
                "name": "AMS CCS811 - eCO2",
                "ancestry": "111",
                "id": 112
            },
            {
                "prev_raw_value": 0,
                "prev_value": 0,
                "raw_value": 0,
                "value": 0,
                "uuid": "ac4234cf-d2b7-4cfa-8765-9f4477e2de5f",
                "measurement_id": 3,
                "updated_at": "2015-07-05T19:57:36Z",
                "created_at": "2015-02-02T18:24:56Z",
                "unit": "Lux",
                "description": "Digital Ambient Light Sensor",
                "name": "BH1730FVC",
                "ancestry": null,
                "id": 14
            },
            {
                "prev_raw_value": 98,
                "prev_value": 98,
                "raw_value": 98,
                "value": 98,
                "uuid": "c9ff2784-53a7-4a84-b0fc-90ecc7e313f9",
                "measurement_id": 7,
                "updated_at": "2015-07-05T19:53:51Z",
                "created_at": "2015-02-02T18:18:00Z",
                "unit": "%",
                "description": "Custom Circuit",
                "name": "Battery SCK 1.1",
                "ancestry": null,
                "id": 10
            },
            {
                "prev_raw_value": 48.72,
                "prev_value": 48.72,
                "raw_value": 48.38,
                "value": 48.38,
                "uuid": "f508548e-3fd1-44aa-839b-9bd147168481",
                "measurement_id": 4,
                "updated_at": "2018-05-03T10:42:54Z",
                "created_at": "2018-05-03T10:42:47Z",
                "unit": "dBA",
                "description": "I2S Digital Mems Microphone with custom Audio Processing Algorithm",
                "name": "ICS43432 - Noise",
                "ancestry": "52",
                "id": 53
            },
            {
                "prev_raw_value": 102.38,
                "prev_value": 102.38,
                "raw_value": 102.38,
                "value": 102.38,
                "uuid": "cadd2459-6559-4d92-aed1-ba04c557fed8",
                "measurement_id": 25,
                "updated_at": "2018-05-03T10:49:17Z",
                "created_at": "2018-05-03T10:49:17Z",
                "unit": "K Pa",
                "description": "Digital Barometric Pressure Sensor",
                "name": "MPL3115A2 - Barometric Pressure",
                "ancestry": "57",
                "id": 58
            },
            {
                "prev_raw_value": 29,
                "prev_value": 29,
                "raw_value": 34,
                "value": 34,
                "uuid": "a4b9efba-241f-446e-9cf2-918f25efd0c5",
                "measurement_id": 27,
                "updated_at": "2018-05-22T13:20:34Z",
                "created_at": "2018-05-22T13:20:34Z",
                "unit": "ug/m3",
                "description": "Particle Matter PM 1",
                "name": "PMS5003_AVG-PM1",
                "ancestry": "86",
                "id": 89
            },
            {
                "prev_raw_value": 32,
                "prev_value": 32,
                "raw_value": 35,
                "value": 35,
                "uuid": "c2072a22-4d81-4d7c-a38c-af9458b8f309",
                "measurement_id": 13,
                "updated_at": "2018-05-22T13:20:34Z",
                "created_at": "2018-05-22T13:20:34Z",
                "unit": "ug/m3",
                "description": "Particle Matter PM 10",
                "name": "PMS5003_AVG-PM10",
                "ancestry": "86",
                "id": 88
            },
            {
                "prev_raw_value": 31,
                "prev_value": 31,
                "raw_value": 35,
                "value": 35,
                "uuid": "9ee89ac2-0482-46dd-905f-0b7a1bb12c55",
                "measurement_id": 14,
                "updated_at": "2018-05-22T13:20:34Z",
                "created_at": "2018-05-22T13:20:34Z",
                "unit": "ug/m3",
                "description": "Particle Matter PM 2.5",
                "name": "PMS5003_AVG-PM2.5",
                "ancestry": "86",
                "id": 87
            },
            {
                "prev_raw_value": 41.39,
                "prev_value": 41.39,
                "raw_value": 41.32,
                "value": 41.32,
                "uuid": "b6543356-0066-4bea-8ad2-687e282f9c20",
                "measurement_id": 2,
                "updated_at": "2018-05-03T10:47:17Z",
                "created_at": "2018-05-03T10:47:17Z",
                "unit": "%",
                "description": "Humidity",
                "name": "SHT31 - Humidity",
                "ancestry": "54",
                "id": 56
            },
            {
                "prev_raw_value": 26.14,
                "prev_value": 26.14,
                "raw_value": 26.22,
                "value": 26.22,
                "uuid": "384e46a2-80dd-481e-a9fc-cfbd512f9f43",
                "measurement_id": 1,
                "updated_at": "2018-05-03T10:47:15Z",
                "created_at": "2018-05-03T10:47:15Z",
                "unit": "ºC",
                "description": "Temperature",
                "name": "SHT31 - Temperature",
                "ancestry": "54",
                "id": 55
            }
        ],
        "location": {
            "country": "Denmark",
            "country_code": "DK",
            "city": "Aarhus",
            "geohash": "u1zr2qkynn",
            "longitude": 10.2130859,
            "latitude": 56.1535618,
            "elevation": null,
            "exposure": "outdoor",
            "ip": null
        },
        "added_at": "2020-06-23T11:53:30Z",
        "recorded_at": "2020-06-23T11:53:30Z"
    },
    "owner": {
        "device_ids": [
            11023,
            13003,
            13004,
            13005
        ],
        "location": {
            "country_code": null,
            "country": null,
            "city": null
        },
        "joined_at": "2019-11-21T09:49:11Z",
        "url": null,
        "avatar": "https://smartcitizen.s3.amazonaws.com/avatars/default.svg",
        "username": "SmartCitySeb",
        "uuid": "28355050-fa23-4db7-b80d-358b64a055c7",
        "id": 7376
    },
    "mac_address": "[FILTERED]",
    "updated_at": "2020-06-23T03:00:22Z",
    "added_at": "2020-06-15T10:26:57Z",
    "last_reading_at": "2020-06-23T11:53:30Z",
    "notify_stopped_publishing": false,
    "notify_low_battery": false,
    "is_private": false,
    "user_tags": [],
    "system_tags": [
        "online",
        "outdoor"
    ],
    "hardware_info": {
        "sam_ver": "0.9.5-a91f850",
        "esp_ver": "0.9.2-a91f850",
        "sam_bd": "2019-08-20T13:25:01Z",
        "hw_ver": "2.1",
        "esp_bd": "2019-08-20T13:17:16Z",
        "time": "2020-06-23T03:00:22Z",
        "mac": "BE:DD:C2:AC:97:EC",
        "id": "417298B850533050352E3120FF13190C"
    },
    "state": "has_published",
    "description": "Smart Citizen Kit 2.1 with Urban Sensor Board",
    "name": "IoTCrawler 4",
    "uuid": "f0cf1a0b-5d7c-4501-acad-f768a9dba3c5",
    "id": 13006
}
JSON),
        ]);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $response = $this->get('/devices');
        $actual = $this->getJson($response);
        $this->assertCount(1, $actual);

        $this->assertArrayHasKey('id', $actual[0]);
        $this->assertEquals('f0cf1a0b-5d7c-4501-acad-f768a9dba3c5', $actual[0]['id']);

        $this->assertArrayHasKey('sensors', $actual[0]);
        $this->assertCount(11, $actual[0]['sensors']);

        $response = $this->post('/smartcitizen', [
            'headers' => [
                'authorization' => 'token api-test-smartcitizen',
            ],
            'json' => json_decode(<<<'JSON'
{
    "kit": {
        "updated_at": "2019-03-21T17:02:46Z",
        "created_at": "2019-03-21T17:02:46Z",
        "description": "Smart Citizen Kit 2.1 with Urban Sensor Board",
        "name": "SCK 2.1",
        "slug": "sck:2,1",
        "uuid": "56bec177-6d93-4001-b700-1abd8347ed87",
        "id": 26
    },
    "data": {
        "sensors": [
            {
                "prev_raw_value": 32,
                "prev_value": 32,
                "raw_value": 32,
                "value": 32,
                "uuid": "0c2a1afc-dc08-4066-aacb-0bde6a3ae6f5",
                "measurement_id": 47,
                "updated_at": "2019-03-21T16:43:37Z",
                "created_at": "2019-03-21T16:43:37Z",
                "unit": "ppb",
                "description": "Total Volatile Organic Compounds Digital Indoor Sensor",
                "name": "AMS CCS811 - TVOC",
                "ancestry": "111",
                "id": 113
            },
            {
                "prev_raw_value": 615,
                "prev_value": 615,
                "raw_value": 615,
                "value": 615,
                "uuid": "995343c9-12ac-40c0-b6b9-19699e524f86",
                "measurement_id": 46,
                "updated_at": "2019-03-21T16:43:37Z",
                "created_at": "2019-03-21T16:43:37Z",
                "unit": "ppm",
                "description": "Equivalent Carbon Dioxide Digital Indoor Sensor",
                "name": "AMS CCS811 - eCO2",
                "ancestry": "111",
                "id": 112
            },
            {
                "prev_raw_value": 805,
                "prev_value": 805,
                "raw_value": 811,
                "value": 811,
                "uuid": "ac4234cf-d2b7-4cfa-8765-9f4477e2de5f",
                "measurement_id": 3,
                "updated_at": "2015-07-05T19:57:36Z",
                "created_at": "2015-02-02T18:24:56Z",
                "unit": "Lux",
                "description": "Digital Ambient Light Sensor",
                "name": "BH1730FVC",
                "ancestry": null,
                "id": 14
            },
            {
                "prev_raw_value": 99,
                "prev_value": 99,
                "raw_value": 99,
                "value": 99,
                "uuid": "c9ff2784-53a7-4a84-b0fc-90ecc7e313f9",
                "measurement_id": 7,
                "updated_at": "2015-07-05T19:53:51Z",
                "created_at": "2015-02-02T18:18:00Z",
                "unit": "%",
                "description": "Custom Circuit",
                "name": "Battery SCK 1.1",
                "ancestry": null,
                "id": 10
            },
            {
                "prev_raw_value": 48.82,
                "prev_value": 48.82,
                "raw_value": 48.47,
                "value": 48.47,
                "uuid": "f508548e-3fd1-44aa-839b-9bd147168481",
                "measurement_id": 4,
                "updated_at": "2018-05-03T10:42:54Z",
                "created_at": "2018-05-03T10:42:47Z",
                "unit": "dBA",
                "description": "I2S Digital Mems Microphone with custom Audio Processing Algorithm",
                "name": "ICS43432 - Noise",
                "ancestry": "52",
                "id": 53
            },
            {
                "prev_raw_value": 102.36,
                "prev_value": 102.36,
                "raw_value": 102.36,
                "value": 102.36,
                "uuid": "cadd2459-6559-4d92-aed1-ba04c557fed8",
                "measurement_id": 25,
                "updated_at": "2018-05-03T10:49:17Z",
                "created_at": "2018-05-03T10:49:17Z",
                "unit": "K Pa",
                "description": "Digital Barometric Pressure Sensor",
                "name": "MPL3115A2 - Barometric Pressure",
                "ancestry": "57",
                "id": 58
            },
            {
                "prev_raw_value": 0,
                "prev_value": 0,
                "raw_value": 0,
                "value": 0,
                "uuid": "a4b9efba-241f-446e-9cf2-918f25efd0c5",
                "measurement_id": 27,
                "updated_at": "2018-05-22T13:20:34Z",
                "created_at": "2018-05-22T13:20:34Z",
                "unit": "ug/m3",
                "description": "Particle Matter PM 1",
                "name": "PMS5003_AVG-PM1",
                "ancestry": "86",
                "id": 89
            },
            {
                "prev_raw_value": 1,
                "prev_value": 1,
                "raw_value": 0,
                "value": 0,
                "uuid": "c2072a22-4d81-4d7c-a38c-af9458b8f309",
                "measurement_id": 13,
                "updated_at": "2018-05-22T13:20:34Z",
                "created_at": "2018-05-22T13:20:34Z",
                "unit": "ug/m3",
                "description": "Particle Matter PM 10",
                "name": "PMS5003_AVG-PM10",
                "ancestry": "86",
                "id": 88
            },
            {
                "prev_raw_value": 1,
                "prev_value": 1,
                "raw_value": 0,
                "value": 0,
                "uuid": "9ee89ac2-0482-46dd-905f-0b7a1bb12c55",
                "measurement_id": 14,
                "updated_at": "2018-05-22T13:20:34Z",
                "created_at": "2018-05-22T13:20:34Z",
                "unit": "ug/m3",
                "description": "Particle Matter PM 2.5",
                "name": "PMS5003_AVG-PM2.5",
                "ancestry": "86",
                "id": 87
            },
            {
                "prev_raw_value": 40.32,
                "prev_value": 40.32,
                "raw_value": 40.34,
                "value": 40.34,
                "uuid": "b6543356-0066-4bea-8ad2-687e282f9c20",
                "measurement_id": 2,
                "updated_at": "2018-05-03T10:47:17Z",
                "created_at": "2018-05-03T10:47:17Z",
                "unit": "%",
                "description": "Humidity",
                "name": "SHT31 - Humidity",
                "ancestry": "54",
                "id": 56
            },
            {
                "prev_raw_value": 26.49,
                "prev_value": 26.49,
                "raw_value": 26.55,
                "value": 26.55,
                "uuid": "384e46a2-80dd-481e-a9fc-cfbd512f9f43",
                "measurement_id": 1,
                "updated_at": "2018-05-03T10:47:15Z",
                "created_at": "2018-05-03T10:47:15Z",
                "unit": "ºC",
                "description": "Temperature",
                "name": "SHT31 - Temperature",
                "ancestry": "54",
                "id": 55
            }
        ],
        "location": {
            "country": "Denmark",
            "country_code": "DK",
            "city": "Aarhus",
            "geohash": "u1zr2qkynn",
            "longitude": 10.2130859,
            "latitude": 56.1535618,
            "elevation": null,
            "exposure": "outdoor",
            "ip": null
        },
        "added_at": "2020-06-23T12:33:17Z",
        "recorded_at": "2020-06-23T12:33:17Z"
    },
    "owner": {
        "device_ids": [
            11023,
            13003,
            13004,
            13005
        ],
        "location": {
            "country_code": null,
            "country": null,
            "city": null
        },
        "joined_at": "2019-11-21T09:49:11Z",
        "url": null,
        "avatar": "https://smartcitizen.s3.amazonaws.com/avatars/default.svg",
        "username": "SmartCitySeb",
        "uuid": "28355050-fa23-4db7-b80d-358b64a055c7",
        "id": 7376
    },
    "mac_address": "[FILTERED]",
    "updated_at": "2020-06-23T03:00:23Z",
    "added_at": "2020-06-15T09:59:09Z",
    "last_reading_at": "2020-06-23T12:33:17Z",
    "notify_stopped_publishing": false,
    "notify_low_battery": false,
    "is_private": false,
    "user_tags": [
        "Experimental"
    ],
    "system_tags": [
        "online",
        "outdoor"
    ],
    "hardware_info": {
        "sam_ver": "0.9.5-a91f850",
        "esp_ver": "",
        "sam_bd": "2019-08-20T13:25:01Z",
        "hw_ver": "2.1",
        "esp_bd": "",
        "time": "2020-06-23T03:00:22Z",
        "mac": "EE:FA:BC:58:F3:90",
        "id": "D81CA28850533050352E3120FF131A07"
    },
    "state": "has_published",
    "description": "Smart Citizen Kit 2.1 with Urban Sensor Board",
    "name": "IoTCrawler 1",
    "uuid": "10adb27d-123e-4ca8-8a59-7ab215a180f5",
    "id": 13003
}
JSON),
        ]);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $response = $this->get('/devices');
        $actual = $this->getJson($response);
        $this->assertCount(2, $actual);

        $response = $this->get('/devices/10adb27d-123e-4ca8-8a59-7ab215a180f5');
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $actual = $this->getJson($response);
        $this->assertArrayHasKey('sensors', $actual);

        $this->assertCount(11, $actual['sensors']);
    }
}
