<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataFixtures;

use App\Entity\Device;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class DeviceFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach ([
            '0004A30B001E1694',
            '0004A30B001E307C',
            '0004A30B001E8EA2',
        ] as $deviceId) {
            $device = (new Device())
                ->setId('aarhus-iot-'.$deviceId)
                ->setName('Aarhus IoT '.$deviceId);

            $manager->persist($device);
            $this->setReference('device:'.$device->getId(), $device);
        }

        $manager->flush();
    }
}
