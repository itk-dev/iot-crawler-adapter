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
use App\SmartConnect\SmartConnect;
use App\Traits\LoggerTrait;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerAwareInterface;

class BrokerMeasurementListener implements EventSubscriber, LoggerAwareInterface
{
    use LoggerTrait;

    /** @var SmartConnect */
    private $smartConnect;

    /** @var array */
    private $config;

    public function __construct(SmartConnect $smartConnect, array $config)
    {
        $this->smartConnect = $smartConnect;
        $this->config = $config;
    }

    public function getSubscribedEvents()
    {
        if (empty($this->config['ngsi_ld_broker_url'])) {
            return [];
        }

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
            $platform = $entity->getSensor()->getDevice()->getId();
            $sensor = $entity->getSensor()->getId();
            $value = 87;
            $time = $entity->getTimestamp();
            try {
                $observation = $this->smartConnect->createObservation($platform, $sensor, $value, $time);
            } catch (\Exception $exception) {
                file_put_contents('/Users/rimi/ITK/iotcrawler/iot-crawler-adapter/hmm.debug', var_export($exception->getMessage(), true), FILE_APPEND);
            }
//             $output->writeln($this->serializer->serialize($observation, 'json'));

             //     // Keep only the latest measurements for the sensor.
        //     $manager = $args->getObjectManager();
        //     $repository = $manager->getRepository(Measurement::class);
        //     $measurements = $repository->findBy(
        //         ['sensor' => $entity->getSensor()],
        //         ['timestamp' => 'DESC'],
        //         null,
        //         $this->config['max_number_of_measurements']
        //     );
        //     foreach ($measurements as $measurement) {
        //         $manager->remove($measurement);
        //     }
        //     $manager->flush();
        }
    }
}
