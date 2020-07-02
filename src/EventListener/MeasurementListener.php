<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\EventListener;

use App\Entity\Measurement;
use App\Entity\Sensor;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class MeasurementListener implements EventSubscriber
{
    /** @var array */
    private $config;

    public function __construct(array $measurementListenerConfig)
    {
        $this->config = $measurementListenerConfig;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
        ];
    }

    // callback methods must be called exactly like the events they listen to;
    // they receive an argument of type LifecycleEventArgs, which gives you access
    // to both the entity object of the event and the entity manager itself
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Measurement) {
            // Keep only the latest measurements for the sensor.
            $manager = $args->getObjectManager();
            $repository = $manager->getRepository(Measurement::class);
            $measurements = $repository->findBy(
                ['sensor' => $entity->getSensor()],
                ['timestamp' => 'DESC'],
                null,
                $this->config['max_number_of_measurements']
            );
            foreach ($measurements as $measurement) {
                $manager->remove($measurement);
            }
            $manager->flush();
        }
    }
}
