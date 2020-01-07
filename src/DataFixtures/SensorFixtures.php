<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataFixtures;

use App\Entity\Sensor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class SensorFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        foreach ([
            '0004A30B001E1694' => [52, 186, 134],
            '0004A30B001E307C' => [52, 74, 76, 77, 79, 186],
            '0004A30B001E8EA2' => [52, 74, 76, 77, 78, 156, 157, 159, 161, 186],
        ] as $deviceId => $sensorIds) {
            $device = $this->getReference('device:aarhus-iot-'.$deviceId);
            foreach ($sensorIds as $sensorId) {
                $sensor = (new Sensor())
                    ->setId($device->getId().'-sensor-'.$sensorId)
                    ->setDevice($device)
                    ->setName('Sensor '.$sensorId);
                $manager->persist($sensor);
                $this->setReference('sensor:'.$sensor->getId(), $sensor);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [DeviceFixtures::class];
    }
}
