<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataFixtures;

use App\Entity\Measurement;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class MeasurementFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $sensor = $this->getReference('sensor:aarhus-iot-0004A30B001E307C-sensor-52');
        $measurement = (new Measurement())
            ->setSensor($sensor)
            ->setSequenceNumber(1)
            ->setTimestamp(new DateTimeImmutable('2020-01-01'))
            ->setPayload(json_decode(<<<'JSON'
{    "data": "5719344eba00004a85eb0ec34c0000c8424d64e5bd474fc500",
    "bat": 255,
    "gws": [
        {
            "gweui": "7076FFFFFF019C05",
            "time": "2020-01-06T11:19:55.544207Z",
            "ts": 1578309595548,
            "snr": 15,
            "rssi": -80
        },
        {
            "gweui": "7076FFFFFF010B88",
            "time": "2020-01-06T11:19:55.443964366Z",
            "ts": 1578309595554,
            "snr": 15,
            "rssi": -86
        },
        {
            "gweui": "7076FFFFFF019BFA",
            "time": "2020-01-06T11:19:55.443987906Z",
            "ts": 1578309595551,
            "snr": 9,
            "rssi": -102
        },
        {
            "gweui": "7076FFFFFF019BFA",
            "time": "2020-01-06T11:19:55.443987906Z",
            "ts": 1578309595551,
            "snr": 5,
            "rssi": -105
        },
        {
            "gweui": "7076FFFFFF019BF7",
            "time": "2020-01-06T11:19:55.443986601Z",
            "ts": 1578309595557,
            "snr": 1,
            "rssi": -106
        },
        {
            "gweui": "7076FFFFFF019BF7",
            "time": "2020-01-06T11:19:55.443986601Z",
            "ts": 1578309595557,
            "snr": 0,
            "rssi": -108
        }
    ],
    "ack": false,
    "dr": "SF7 BW125 4/5",
    "toa": 0,
    "freq": 867300000,
    "port": 4,
    "fcnt": 5207,
    "ts": 1578309595548,
    "EUI": "0004A30B001E307C",
    "seqno": 225759,
    "cmd": "gw"
}
JSON
, true))
        ->setDataFormat('0004A30B001E307C');

        $manager->persist($measurement);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [SensorFixtures::class];
    }
}
